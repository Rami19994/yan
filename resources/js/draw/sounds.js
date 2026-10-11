/**
 * Optional sound effects. Disabled by default (browsers block autoplay audio
 * until the user interacts with the page, and the spec asks for an explicit
 * ON/OFF toggle). Audio files are NOT bundled — drop .mp3 files at the paths
 * below to enable them; playback is wrapped in try/catch so a missing file
 * never breaks the draw experience.
 */
const SOURCES = {
    spin: '/sounds/draw-spin.mp3',
    stop: '/sounds/draw-stop.mp3',
    suspense: '/sounds/draw-suspense.mp3',
    winner: '/sounds/draw-winner.mp3',
};

export class DrawSounds {
    constructor() {
        this.enabled = false;
        this.elements = {};

        for (const [key, src] of Object.entries(SOURCES)) {
            const audio = new Audio(src);
            audio.preload = 'none';
            audio.volume = key === 'spin' ? 0.35 : 0.6;
            this.elements[key] = audio;
        }
    }

    setEnabled(enabled) {
        this.enabled = enabled;
    }

    play(key) {
        if (!this.enabled) {
            return;
        }

        const audio = this.elements[key];

        if (!audio) {
            return;
        }

        try {
            audio.currentTime = 0;
            audio.play()?.catch(() => {
                // Autoplay blocked or file missing — fail silently.
            });
        } catch {
            // Ignore — sound is a non-critical enhancement.
        }
    }
}
