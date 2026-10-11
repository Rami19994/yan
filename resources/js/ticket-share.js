// Wraps LTR content (phone numbers, dates) in directional isolate marks so
// it renders as a single left-to-right run instead of having its
// space-separated groups reordered by the bidi algorithm inside RTL text.
function isolateLtr(value) {
    return `⁦${value}⁩`;
}

function loadImage(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}

function roundedRectPath(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
}

function drawGoldText(ctx, text, x, y, fontSize, weight = 800) {
    ctx.save();
    ctx.font = `${weight} ${fontSize}px sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'alphabetic';

    ctx.shadowColor = 'rgba(0,0,0,0.6)';
    ctx.shadowBlur = 10;
    ctx.shadowOffsetY = 4;

    const gradient = ctx.createLinearGradient(0, y - fontSize, 0, y + fontSize * 0.25);
    gradient.addColorStop(0, '#fff6d6');
    gradient.addColorStop(0.35, '#f5c542');
    gradient.addColorStop(0.7, '#caa02e');
    gradient.addColorStop(1, '#8a6d1f');
    ctx.fillStyle = gradient;
    ctx.fillText(text, x, y);

    ctx.shadowColor = 'transparent';
    ctx.lineWidth = Math.max(2, fontSize * 0.02);
    ctx.strokeStyle = '#5c4512';
    ctx.strokeText(text, x, y);
    ctx.restore();
}

function drawCornerBracket(ctx, x, y, size, rotate) {
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(rotate);
    ctx.strokeStyle = 'rgba(245,197,66,0.55)';
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.moveTo(0, size);
    ctx.lineTo(0, 0);
    ctx.lineTo(size, 0);
    ctx.stroke();
    ctx.strokeStyle = 'rgba(63,208,255,0.4)';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(14, size - 4);
    ctx.lineTo(14, 14);
    ctx.lineTo(size - 4, 14);
    ctx.stroke();
    ctx.restore();
}

async function buildTicketImage(config) {
    const width = 1600;
    const height = 900;
    const centerX = width / 2;

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.direction = config.dir === 'rtl' ? 'rtl' : 'ltr';

    // Base background.
    ctx.fillStyle = '#060d1a';
    ctx.fillRect(0, 0, width, height);

    // Outer holographic border.
    const rainbow = ctx.createLinearGradient(0, 0, width, height);
    ['#ff6b6b', '#ffb86b', '#ffe56b', '#8bff9e', '#6bd4ff', '#8f8bff', '#ff8bf0'].forEach((color, i, arr) => {
        rainbow.addColorStop(i / (arr.length - 1), color);
    });
    roundedRectPath(ctx, 8, 8, width - 16, height - 16, 40);
    ctx.strokeStyle = rainbow;
    ctx.lineWidth = 14;
    ctx.stroke();

    // Panel background.
    const panelBg = ctx.createLinearGradient(0, 30, 0, height - 30);
    panelBg.addColorStop(0, '#0d1a2e');
    panelBg.addColorStop(1, '#060e1c');
    roundedRectPath(ctx, 30, 30, width - 60, height - 60, 32);
    ctx.fillStyle = panelBg;
    ctx.fill();

    ctx.save();
    roundedRectPath(ctx, 46, 46, width - 92, height - 92, 26);
    ctx.strokeStyle = '#caa02e';
    ctx.lineWidth = 3;
    ctx.shadowColor = 'rgba(245,197,66,0.4)';
    ctx.shadowBlur = 8;
    ctx.stroke();
    ctx.restore();

    roundedRectPath(ctx, 56, 56, width - 112, height - 112, 22);
    ctx.strokeStyle = 'rgba(63,208,255,0.35)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    [[70, 70, 0], [width - 70, 70, Math.PI / 2], [width - 70, height - 70, Math.PI], [70, height - 70, -Math.PI / 2]]
        .forEach(([x, y, rotate]) => drawCornerBracket(ctx, x, y, 50, rotate));

    // Title.
    ctx.font = '800 76px sans-serif';
    const brandWidth = ctx.measureText(config.brand).width;
    drawGoldText(ctx, config.brand, centerX, 150, 76);
    drawGoldText(ctx, '✦', centerX - brandWidth / 2 - 56, 142, 44, 700);
    drawGoldText(ctx, '✦', centerX + brandWidth / 2 + 56, 142, 44, 700);

    drawGoldText(ctx, `★ ${config.tagline} ★`, centerX, 205, 32, 600);

    // Double rule.
    ctx.strokeStyle = 'rgba(245,197,66,0.5)';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(140, 235);
    ctx.lineTo(width - 140, 235);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(140, 241);
    ctx.lineTo(width - 140, 241);
    ctx.stroke();

    // Info block.
    ctx.textAlign = 'center';
    ctx.fillStyle = '#e8eef7';
    ctx.font = '600 36px sans-serif';
    const infoLines = [
        `${config.nameLabel}: ${config.customerName}`,
        `${config.phoneLabel}: ${isolateLtr(config.customerPhone)}`,
        `${config.drawDateLabel}: ${isolateLtr(config.drawDate)}`,
    ];
    let infoY = 300;
    for (const line of infoLines) {
        ctx.fillText(line, centerX, infoY);
        infoY += 52;
    }

    // Lucky-number divider with label cut into the line.
    const dividerY = 490;
    const lineGradient = ctx.createLinearGradient(140, 0, width - 140, 0);
    lineGradient.addColorStop(0, 'rgba(245,197,66,0)');
    lineGradient.addColorStop(0.5, 'rgba(245,197,66,0.8)');
    lineGradient.addColorStop(1, 'rgba(245,197,66,0)');
    ctx.strokeStyle = lineGradient;
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(140, dividerY);
    ctx.lineTo(width - 140, dividerY);
    ctx.stroke();

    ctx.font = '700 34px sans-serif';
    const labelWidth = ctx.measureText(config.luckyNumberLabel).width;
    ctx.fillStyle = '#0d1a2e';
    ctx.fillRect(centerX - labelWidth / 2 - 24, dividerY - 28, labelWidth + 48, 44);
    ctx.fillStyle = '#f5c542';
    ctx.fillText(config.luckyNumberLabel, centerX, dividerY + 10);

    // Digit boxes.
    const digits = String(config.ticketNumber).split('');
    const boxW = 128;
    const boxH = 150;
    const gap = 16;
    const totalW = digits.length * boxW + (digits.length - 1) * gap;
    let boxX = centerX - totalW / 2;
    const boxY = 545;

    for (const digit of digits) {
        const boxGradient = ctx.createLinearGradient(0, boxY, 0, boxY + boxH);
        boxGradient.addColorStop(0, '#223654');
        boxGradient.addColorStop(1, '#0f1b2e');
        roundedRectPath(ctx, boxX, boxY, boxW, boxH, 16);
        ctx.fillStyle = boxGradient;
        ctx.fill();
        ctx.strokeStyle = '#caa02e';
        ctx.lineWidth = 3;
        ctx.stroke();

        ctx.fillStyle = '#ffd94d';
        ctx.font = '800 92px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(digit, boxX + boxW / 2, boxY + boxH / 2 + 32);

        boxX += boxW + gap;
    }

    // Verification QR, tucked in a corner.
    const qrSize = 118;
    const qrX = 90;
    const qrY = 90;
    const qrImg = await loadImage(config.qrDataUri);
    roundedRectPath(ctx, qrX - 10, qrY - 10, qrSize + 20, qrSize + 20, 14);
    ctx.fillStyle = '#ffffff';
    ctx.fill();
    ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

    // Footer.
    ctx.fillStyle = '#f1d9a0';
    ctx.font = '700 28px sans-serif';
    ctx.fillText(config.footerLine, centerX, height - 90);

    ctx.fillStyle = '#7d8aa0';
    ctx.font = '400 22px sans-serif';
    ctx.fillText(config.disclaimer, centerX, height - 56);

    return new Promise((resolve) => canvas.toBlob(resolve, 'image/png', 0.95));
}

async function shareTicketImage(button, config) {
    const originalLabel = button.textContent;
    button.disabled = true;
    button.textContent = config.generatingLabel;

    try {
        const blob = await buildTicketImage(config);
        const file = new File([blob], `yanasib-ticket-${config.ticketNumber}.png`, { type: 'image/png' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ files: [file], title: config.brand, text: config.shareText });
        } else {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = file.name;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
            window.alert(config.fallbackMessage);
        }
    } catch (error) {
        if (error?.name !== 'AbortError') {
            console.error('Ticket share failed', error);
            window.alert(config.errorMessage);
        }
    } finally {
        button.disabled = false;
        button.textContent = originalLabel;
    }
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-ticket-share]');
    if (! button) return;

    shareTicketImage(button, JSON.parse(button.dataset.ticketShare));
});
