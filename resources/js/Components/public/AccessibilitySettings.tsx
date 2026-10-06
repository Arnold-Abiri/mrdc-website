import { useEffect, useState } from 'react';
import { usePublicTranslation } from '../../usePublicTranslation';

const FONT_SCALES = [87.5, 100, 112.5, 125];
const DEFAULT_SCALE_INDEX = 1;

function readScale(): number {
    const stored = typeof window === 'undefined' ? null : window.localStorage.getItem('a11y-font-scale');
    const parsed = stored === null ? Number.NaN : Number(stored);
    return FONT_SCALES.includes(parsed) ? parsed : FONT_SCALES[DEFAULT_SCALE_INDEX];
}

function readContrast(): boolean {
    return typeof window !== 'undefined' && window.localStorage.getItem('a11y-high-contrast') === '1';
}

export default function AccessibilitySettings() {
    const t = usePublicTranslation();
    const [scale, setScale] = useState<number>(FONT_SCALES[DEFAULT_SCALE_INDEX]);
    const [contrast, setContrast] = useState(false);

    useEffect(() => {
        setScale(readScale());
        setContrast(readContrast());
    }, []);

    useEffect(() => {
        document.documentElement.style.fontSize = `${scale}%`;
        window.localStorage.setItem('a11y-font-scale', String(scale));
    }, [scale]);

    useEffect(() => {
        document.documentElement.classList.toggle('a11y-high-contrast', contrast);
        window.localStorage.setItem('a11y-high-contrast', contrast ? '1' : '0');
    }, [contrast]);

    const step = (direction: -1 | 1) => {
        const index = FONT_SCALES.indexOf(scale);
        const next = FONT_SCALES[Math.min(FONT_SCALES.length - 1, Math.max(0, index + direction))];
        setScale(next ?? scale);
    };

    return (
        <div className="a11y-settings" role="group" aria-label={t('accessibility')}>
            <span className="a11y-settings-label" aria-hidden="true">A</span>
            <button type="button" className="a11y-settings-button" aria-label={t('decreaseFontSize')} onClick={() => step(-1)} disabled={scale <= FONT_SCALES[0]}>A−</button>
            <button type="button" className="a11y-settings-button" aria-label={t('resetFontSize')} onClick={() => setScale(FONT_SCALES[DEFAULT_SCALE_INDEX])}>A</button>
            <button type="button" className="a11y-settings-button" aria-label={t('increaseFontSize')} onClick={() => step(1)} disabled={scale >= FONT_SCALES[FONT_SCALES.length - 1]}>A+</button>
            <button type="button" className="a11y-settings-button" aria-label={t('highContrast')} aria-pressed={contrast} onClick={() => setContrast(!contrast)}>◐</button>
        </div>
    );
}
