import * as THREE from 'three';
import gsap from 'gsap';
import { Wheel } from './wheel';
import { createParticleField } from './particles';
import { createConfettiBurst } from './confetti';

const WHEEL_COUNT = 7;
const WHEEL_SPACING = 1.5;
const WHEEL_SPIN_DURATION = 10;
const WHEEL_STOP_STAGGER = 1.3;

export class DrawScene {
    constructor(canvas) {
        this.canvas = canvas;
        this.scene = new THREE.Scene();
        // Solid (not transparent) background: the digit-reel masks in wheel.js
        // need to match this exact color to blend seamlessly, which a
        // transparent canvas over a CSS gradient could never do consistently.
        this.scene.background = new THREE.Color(0x02040a);
        this.scene.fog = new THREE.FogExp2(0x02040a, 0.045);

        this.camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);
        this.camera.position.set(0, 0.4, 11);
        this.camera.lookAt(0, 0.4, 0);
        this.baseCameraZ = this.camera.position.z;

        this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;

        this._setupLighting();
        this._setupWheels();
        this.particles = createParticleField(this.scene);
        this._particleSpeed = 0.0006;

        this._resize();
        this._animate = this._animate.bind(this);
        this._raf = requestAnimationFrame(this._animate);

        // A plain window 'resize' listener isn't enough: the canvas's parent
        // uses a Tailwind aspect-ratio class, and its real clientWidth/Height
        // may not have settled yet at construction time (seen as the canvas
        // staying stuck at the browser's intrinsic 300x150 default). A
        // ResizeObserver fires as soon as the parent's actual box is known,
        // and again on every later layout change.
        this._resizeObserver = new ResizeObserver(() => this._resize());
        this._resizeObserver.observe(this.canvas.parentElement);

        // Defensive belt-and-braces: re-measure a couple of frames later too,
        // in case the very first layout pass (synchronous, constructor-time)
        // raced ahead of CSS/font application in some browser.
        requestAnimationFrame(() => requestAnimationFrame(() => this._resize()));
    }

    _setupLighting() {
        const ambient = new THREE.AmbientLight(0x404a66, 0.6);
        this.scene.add(ambient);

        const gold = new THREE.PointLight(0xfbbf24, 2.2, 30);
        gold.position.set(-4, 3, 6);
        this.scene.add(gold);

        const blueRim = new THREE.PointLight(0x38bdf8, 1.4, 30);
        blueRim.position.set(5, -2, 4);
        this.scene.add(blueRim);

        const keyLight = new THREE.DirectionalLight(0xffffff, 0.5);
        keyLight.position.set(0, 5, 8);
        this.scene.add(keyLight);
    }

    _setupWheels() {
        this.wheels = [];
        const totalWidth = (WHEEL_COUNT - 1) * WHEEL_SPACING;

        for (let i = 0; i < WHEEL_COUNT; i++) {
            const x = i * WHEEL_SPACING - totalWidth / 2;
            // More cycles than before so the lengthened spin still reads as
            // genuinely fast during its cruise phase instead of looking
            // under-sped over a longer duration.
            this.wheels.push(new Wheel(this.scene, x, 18 + i * 2));
        }
    }

    _resize() {
        const { clientWidth, clientHeight } = this.canvas.parentElement;
        const width = Math.max(clientWidth, 1);
        const height = Math.max(clientHeight, 1);

        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setSize(width, height, false);
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
    }

    _animate() {
        this._raf = requestAnimationFrame(this._animate);

        if (this.particles) {
            this.particles.rotation.y += this._particleSpeed;
        }

        this.renderer.render(this.scene, this.camera);
    }

    /**
     * Spin all 7 wheels to reveal `number` (a 7-digit integer). Each wheel
     * spins for a full WHEEL_SPIN_DURATION seconds, but they start on a
     * stagger (WHEEL_STOP_STAGGER apart) so they land one after another,
     * left to right, like a real multi-reel machine — not all freezing at
     * once. Resolves once the last wheel has stopped.
     */
    spinToNumber(number, { reducedMotion = false, onWheelStop = null } = {}) {
        const digits = String(number).padStart(7, '0').split('').map(Number);

        if (reducedMotion) {
            return new Promise((resolve) => {
                this.wheels.forEach((wheel, i) => {
                    wheel.reset();
                    wheel.group.position.y = (wheel.cycles * 10 + digits[i]) * 1.3;
                    wheel.currentDigit = digits[i];
                });
                onWheelStop?.(WHEEL_COUNT - 1);
                resolve();
            });
        }

        this._startSpinCamera();

        return new Promise((resolve) => {
            this.wheels.forEach((wheel, i) => {
                wheel.spinTo(digits[i], {
                    delay: i * WHEEL_STOP_STAGGER,
                    duration: WHEEL_SPIN_DURATION,
                    ease: 'power3.out',
                    onStop: () => {
                        onWheelStop?.(i);
                        if (i === WHEEL_COUNT - 1) {
                            this._stopSpinCamera();
                            resolve();
                        }
                    },
                });
            });
        });
    }

    resetWheels() {
        this._cameraDriftTween?.kill();
        this._particleSpeedTween?.kill();
        this.camera.position.set(0, 0.4, this.baseCameraZ);
        this._particleSpeed = 0.0006;
        this.wheels.forEach((wheel) => wheel.reset());
    }

    /**
     * Subtle continuous sway + faster ambient particles while the wheels are
     * spinning, so the long 10-second-per-wheel sequence stays visually
     * alive instead of sitting on a perfectly static camera the whole time.
     */
    _startSpinCamera() {
        this._particleSpeedTween?.kill();
        this._particleSpeedTween = gsap.to(this, {
            _particleSpeed: 0.0022,
            duration: 1.5,
            ease: 'power2.out',
        });

        this._cameraDriftTween?.kill();
        this._cameraDriftTween = gsap.to(this.camera.position, {
            x: 0.18,
            y: 0.52,
            duration: 3.5,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1,
        });
    }

    _stopSpinCamera() {
        this._cameraDriftTween?.kill();
        gsap.to(this.camera.position, {
            x: 0,
            y: 0.4,
            duration: 1.2,
            ease: 'power2.out',
        });

        this._particleSpeedTween?.kill();
        this._particleSpeedTween = gsap.to(this, {
            _particleSpeed: 0.0006,
            duration: 1.5,
            ease: 'power2.out',
        });
    }

    /**
     * Cinematic push-in + confetti + light burst for a winning reveal.
     */
    playWinnerEffect() {
        gsap.to(this.camera.position, {
            z: this.baseCameraZ - 1.2,
            duration: 1.4,
            ease: 'power2.out',
            yoyo: true,
            repeat: 1,
        });

        createConfettiBurst(this.scene);
    }

    dispose() {
        cancelAnimationFrame(this._raf);
        this._resizeObserver.disconnect();
        this._cameraDriftTween?.kill();
        this._particleSpeedTween?.kill();
        this.wheels.forEach((wheel) => wheel.dispose());
        this.renderer.dispose();
    }
}
