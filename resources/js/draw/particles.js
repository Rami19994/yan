import * as THREE from 'three';

/**
 * A slowly drifting field of small glowing points for ambience behind the wheels.
 */
export function createParticleField(scene, count = 300) {
    const positions = new Float32Array(count * 3);

    for (let i = 0; i < count; i++) {
        positions[i * 3] = (Math.random() - 0.5) * 30;
        positions[i * 3 + 1] = (Math.random() - 0.5) * 18;
        positions[i * 3 + 2] = (Math.random() - 0.5) * 20 - 5;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    const material = new THREE.PointsMaterial({
        color: 0xfbbf24,
        size: 0.045,
        transparent: true,
        opacity: 0.45,
        depthWrite: false,
    });

    const points = new THREE.Points(geometry, material);
    scene.add(points);

    return points;
}
