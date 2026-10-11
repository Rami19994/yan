import * as THREE from 'three';

/**
 * Renders a single digit (0-9) onto a canvas and returns a Three.js texture.
 * Shared/cached per digit so all 7 wheels reuse the same 10 textures instead
 * of creating redundant canvases.
 */
const cache = new Map();

function renderDigitCanvas(digit) {
    const size = 256;
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');

    // Dark glass panel background.
    const bg = ctx.createLinearGradient(0, 0, 0, size);
    bg.addColorStop(0, '#0b1220');
    bg.addColorStop(1, '#02050a');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, size, size);

    // Subtle panel border.
    ctx.strokeStyle = 'rgba(251, 191, 36, 0.25)';
    ctx.lineWidth = 6;
    ctx.strokeRect(6, 6, size - 12, size - 12);

    // Glowing gold digit.
    ctx.font = '700 180px "Segoe UI", system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    ctx.shadowColor = 'rgba(251, 191, 36, 0.85)';
    ctx.shadowBlur = 28;
    ctx.fillStyle = '#fbbf24';
    ctx.fillText(String(digit), size / 2, size / 2 + 10);

    ctx.shadowBlur = 0;
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.fillText(String(digit), size / 2, size / 2 + 10);

    return canvas;
}

export function getDigitTexture(digit) {
    if (cache.has(digit)) {
        return cache.get(digit);
    }

    const texture = new THREE.CanvasTexture(renderDigitCanvas(digit));
    texture.colorSpace = THREE.SRGBColorSpace;
    cache.set(digit, texture);

    return texture;
}
