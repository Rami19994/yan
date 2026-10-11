import { DrawScene } from './draw/scene';
import { DrawSounds } from './draw/sounds';
import { isWebGLAvailable, prefersReducedMotion } from './draw/webgl-detect';

/**
 * Drives the 3D draw stage on a page. The result number always comes from
 * the backend (via a Livewire-dispatched browser event carrying the number
 * the server already generated with random_int()) — this module only
 * animates a reveal of that number, it never invents one itself.
 */
class DrawStage {
    constructor(root) {
        this.root = root;
        this.canvas = root.querySelector('[data-draw-canvas]');
        this.fallback = root.querySelector('[data-draw-fallback]');
        this.stageEl = root.querySelector('[data-draw-stage]');
        this.digitsEl = root.querySelector('[data-draw-digits]');
        this.statusEl = root.querySelector('[data-draw-status]');
        this.winnerPanel = root.querySelector('[data-draw-winner-panel]');
        this.previewBadge = root.querySelector('[data-draw-preview-badge]');
        this.soundToggle = root.querySelector('[data-draw-sound-toggle]');
        this.fullscreenToggle = root.querySelector('[data-draw-fullscreen-toggle]');

        this.sounds = new DrawSounds();
        this.reducedMotion = prefersReducedMotion();
        this.webglOk = isWebGLAvailable();

        if (this.webglOk && this.canvas) {
            this.scene = new DrawScene(this.canvas);
        } else if (this.fallback) {
            this.fallback.classList.remove('hidden');
            this.canvas?.classList.add('hidden');
        }

        this._bindControls();
    }

    _bindControls() {
        this.soundToggle?.addEventListener('click', () => {
            const enabled = this.soundToggle.getAttribute('aria-pressed') !== 'true';
            this.soundToggle.setAttribute('aria-pressed', String(enabled));
            this.soundToggle.textContent = enabled ? '🔊' : '🔇';
            this.sounds.setEnabled(enabled);
        });

        this.fullscreenToggle?.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                this.stageEl?.requestFullscreen?.().catch(() => {});
            } else {
                document.exitFullscreen?.();
            }
        });
    }

    reset() {
        this.scene?.resetWheels();
        this.digitsEl && (this.digitsEl.textContent = '');
        this.statusEl && (this.statusEl.textContent = '');
        this.winnerPanel?.classList.add('hidden');
    }

    _el(tag, className, text) {
        const node = document.createElement(tag);
        node.className = className;
        node.textContent = text;

        return node;
    }

    /**
     * @param {object} payload
     * @param {number} payload.number - 7-digit result from the backend.
     * @param {'preview'|'official'} payload.mode
     * @param {object|null} payload.winner - { ticketNumber, customerName, prizeAmount, cashierName, masterName } or null.
     * @param {boolean} payload.noWinner
     */
    async play(payload) {
        this.reset();

        if (this.previewBadge) {
            this.previewBadge.classList.toggle('hidden', payload.mode !== 'preview');
        }

        this.statusEl && (this.statusEl.textContent = '');
        this.sounds.play('spin');

        if (this.scene) {
            await this.scene.spinToNumber(payload.number, {
                reducedMotion: this.reducedMotion,
                onWheelStop: () => this.sounds.play('stop'),
            });
        }

        this.digitsEl && (this.digitsEl.textContent = String(payload.number).padStart(7, '0'));

        if (this.statusEl) {
            this.statusEl.textContent = 'Checking ticket...';
        }
        this.sounds.play('suspense');

        await new Promise((resolve) => setTimeout(resolve, this.reducedMotion ? 150 : 1200));

        if (payload.winner) {
            this._showWinner(payload.winner, payload.mode);
        } else {
            this._showNoWinner(payload.mode);
        }
    }

    _showWinner(winner, mode) {
        if (this.statusEl) {
            this.statusEl.textContent = mode === 'preview' ? 'PREVIEW — Winner would be:' : 'WINNER!';
        }

        if (this.winnerPanel) {
            this.winnerPanel.classList.remove('hidden');
            // Built via DOM methods + textContent rather than innerHTML
            // string interpolation: customerName/cashierName/masterName are
            // user-supplied (a cashier types the buyer's name at sale time)
            // and this panel renders on the PUBLIC, unauthenticated live
            // draw screen — interpolating them into innerHTML would be a
            // stored XSS vector reachable by any cashier account.
            this.winnerPanel.replaceChildren(
                this._el('div', 'text-3xl', '🎉'),
                this._el('div', 'mt-2 text-xl font-bold text-amber-400', `#${winner.ticketNumber}`),
                this._el('div', 'mt-1 text-slate-200', winner.customerName ?? ''),
                this._el(
                    'div',
                    'mt-1 text-sm text-slate-400',
                    winner.prizeAmount ? `${Number(winner.prizeAmount).toLocaleString()} IQD (virtual)` : ''
                ),
                this._el(
                    'div',
                    'mt-1 text-xs text-slate-500',
                    [winner.cashierName, winner.masterName].filter(Boolean).join(' · ')
                ),
            );
        }

        if (mode === 'official') {
            this.sounds.play('winner');
            this.scene?.playWinnerEffect();
        }
    }

    _showNoWinner(mode) {
        if (this.statusEl) {
            this.statusEl.textContent = mode === 'preview' ? 'PREVIEW — no ticket matches this number' : 'No winner this round';
        }
    }

    dispose() {
        this.scene?.dispose();
    }
}

function boot() {
    document.querySelectorAll('[data-draw-root]').forEach((root) => {
        const stage = new DrawStage(root);
        window.YanasibDraw = stage;

        if (window.Livewire) {
            window.Livewire.on('draw-result', (payload) => stage.play(payload));
            window.Livewire.on('draw-reset', () => stage.reset());
        }

        window.dispatchEvent(new CustomEvent('draw-stage-ready'));
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
