import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicLayout from './PublicLayout';

describe('public navigation', () => {
    it('opens on mobile and exposes its state', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        const menu = screen.getByRole('button', { name: 'Menu' });
        expect(menu).toHaveAttribute('aria-expanded', 'false');
        fireEvent.click(menu);
        expect(screen.getByRole('button', { name: 'Close menu' })).toHaveAttribute('aria-expanded', 'true');
    });
});
