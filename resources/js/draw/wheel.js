import * as THREE from 'three';
import gsap from 'gsap';
import { getDigitTexture } from './digit-texture';

const DIGIT_HEIGHT = 1.3;
const DIGIT_WIDTH = 1.0;

/**
 * A single spinning digit reel (one of the 7 wheels). Built as a vertical
 * column of digit planes inside a group; "spinning" moves the group down a
 * long distance with an easing curve so it looks like it accelerates then
 * decelerates onto the target digit, landing exactly on the reading line
 * (y = 0 inside the wheel's local space).
 */
export class Wheel {
    constructor(scene, x, cycles = 10) {
        this.group = new THREE.Group();
        this.group.position.x = x;
        this.cycles = cycles;
        this.currentDigit = 0;

        const totalRows = (cycles + 2) * 10;

        for (let row = 0; row < totalRows; row++) {
            const digit = row % 10;
            const geometry = new THREE.PlaneGeometry(DIGIT_WIDTH, DIGIT_HEIGHT * 0.96);
            const material = new THREE.MeshBasicMaterial({
                map: getDigitTexture(digit),
                transparent: true,
            });
            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.y = -row * DIGIT_HEIGHT;
            this.group.add(mesh);
        }

        // Metallic bezel frame around the reel window.
        const frameGeometry = new THREE.PlaneGeometry(DIGIT_WIDTH * 1.25, DIGIT_HEIGHT * 1.15);
        const frameMaterial = new THREE.MeshStandardMaterial({
            color: 0x1a2235,
            metalness: 0.85,
            roughness: 0.25,
            emissive: 0x0a0e1a,
        });
        this.frame = new THREE.Mesh(frameGeometry, frameMaterial);
        this.frame.position.z = -0.05;

        // Opaque occluders above/below the window so only the centered digit
        // is visible — without these every digit in the reel column would
        // show at once since nothing else clips the view.
        this.maskTop = this._createMask();
        this.maskTop.position.y = DIGIT_HEIGHT * 0.58 + 7.5;
        this.maskBottom = this._createMask();
        this.maskBottom.position.y = -(DIGIT_HEIGHT * 0.58 + 7.5);

        this.windowMask = new THREE.Group();
        this.windowMask.add(this.frame);
        this.windowMask.add(this.group);
        this.windowMask.add(this.maskTop);
        this.windowMask.add(this.maskBottom);
        this.windowMask.position.x = x;
        this.group.position.x = 0;

        scene.add(this.windowMask);
    }

    _createMask() {
        // Matches DrawScene's solid background color exactly so the occluder
        // blends in seamlessly rather than appearing as a visible patch.
        const geometry = new THREE.PlaneGeometry(DIGIT_WIDTH * 1.4, 15);
        const material = new THREE.MeshBasicMaterial({ color: 0x02040a, fog: false });
        const mesh = new THREE.Mesh(geometry, material);
        mesh.position.z = 0.08;

        return mesh;
    }

    /**
     * Spin and land on `targetDigit` (0-9) after `delay` seconds, taking
     * `duration` seconds total.
     *
     * Built as two sequential phases rather than one long eased tween: a
     * fast, constant-speed "cruise" for the first part of the spin (the
     * classic slot-machine blur), then a long, smoothly decelerating
     * "brake" into the exact target digit for the rest. A single
     * power-eased tween spread over many seconds would visibly crawl from
     * frame one instead of reading as something that starts fast and
     * gradually grinds to a stop.
     */
    spinTo(targetDigit, { delay = 0, duration = 10, ease = 'power3.out', onStop = null } = {}) {
        const spins = this.cycles;
        const targetY = (spins * 10 + targetDigit) * DIGIT_HEIGHT;

        this.currentDigit = targetDigit;

        const cruiseDuration = duration * 0.55;
        const brakeDuration = duration - cruiseDuration;
        const cruiseY = targetY * 0.68;

        const timeline = gsap.timeline({ delay });

        timeline.to(this.group.position, {
            y: cruiseY,
            duration: cruiseDuration,
            ease: 'none',
        });

        timeline.to(this.group.position, {
            y: targetY,
            duration: brakeDuration,
            ease,
            onComplete: () => {
                this._flashLanding();
                onStop?.(targetDigit);
            },
        });

        return timeline;
    }

    /**
     * A brief emissive pulse + scale bounce on the bezel the instant a wheel
     * locks onto its final digit — visual "thunk" feedback so the cascade of
     * wheels stopping one after another actually reads as each one landing,
     * not just silently halting.
     */
    _flashLanding() {
        const idleEmissive = 0x0a0e1a;

        this.frame.material.emissive.setHex(0xfbbf24);
        gsap.fromTo(
            this.frame.material,
            { emissiveIntensity: 1.6 },
            {
                emissiveIntensity: 1,
                duration: 0.6,
                ease: 'power2.out',
                onComplete: () => this.frame.material.emissive.setHex(idleEmissive),
            },
        );

        gsap.fromTo(
            this.windowMask.scale,
            { x: 1.06, y: 1.06, z: 1 },
            { x: 1, y: 1, z: 1, duration: 0.35, ease: 'back.out(3)' },
        );
    }

    reset() {
        gsap.killTweensOf(this.group.position);
        gsap.killTweensOf(this.frame.material);
        gsap.killTweensOf(this.windowMask.scale);
        this.group.position.y = 0;
        this.windowMask.scale.set(1, 1, 1);
        this.frame.material.emissive.setHex(0x0a0e1a);
        this.frame.material.emissiveIntensity = 1;
        this.currentDigit = 0;
    }

    dispose() {
        this.group.children.forEach((mesh) => {
            mesh.geometry.dispose();
            mesh.material.dispose();
        });
        [this.frame, this.maskTop, this.maskBottom].forEach((mesh) => {
            mesh.geometry.dispose();
            mesh.material.dispose();
        });
    }
}
