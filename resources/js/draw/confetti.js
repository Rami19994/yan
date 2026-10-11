import * as THREE from 'three';
import gsap from 'gsap';

const COLORS = [0xfbbf24, 0xf59e0b, 0x38bdf8, 0xffffff, 0xf472b6];

/**
 * A short-lived burst of small tumbling confetti pieces for the winner reveal.
 * Each piece animates itself out and disposes its own geometry when done.
 */
export function createConfettiBurst(scene, count = 90) {
    const group = new THREE.Group();
    scene.add(group);

    for (let i = 0; i < count; i++) {
        const geometry = new THREE.PlaneGeometry(0.12, 0.2);
        const material = new THREE.MeshBasicMaterial({
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
            side: THREE.DoubleSide,
            transparent: true,
        });
        const piece = new THREE.Mesh(geometry, material);

        piece.position.set((Math.random() - 0.5) * 6, 4 + Math.random() * 2, (Math.random() - 0.5) * 3);
        piece.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
        group.add(piece);

        const fallDuration = 2.2 + Math.random() * 1.4;

        gsap.to(piece.position, {
            y: -6 - Math.random() * 2,
            x: piece.position.x + (Math.random() - 0.5) * 3,
            duration: fallDuration,
            ease: 'power1.in',
        });

        gsap.to(piece.rotation, {
            x: piece.rotation.x + Math.random() * 10,
            y: piece.rotation.y + Math.random() * 10,
            duration: fallDuration,
            ease: 'none',
        });

        gsap.to(material, {
            opacity: 0,
            duration: 0.6,
            delay: fallDuration - 0.6,
            onComplete: () => {
                geometry.dispose();
                material.dispose();
                group.remove(piece);
            },
        });
    }

    gsap.delayedCall(4, () => scene.remove(group));
}
