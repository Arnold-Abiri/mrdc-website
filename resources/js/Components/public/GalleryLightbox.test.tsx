import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/react')>()),
    usePage: () => ({ props: { locale: 'en', localeCsrfToken: 'test-token' } }),
}));

import GalleryLightbox, { type GalleryImage } from './GalleryLightbox';

const images: GalleryImage[] = [
    { url: '/en/managed-media/1', alt: 'First image', caption: 'First caption', width: 1200, height: 800 },
    { url: '/en/managed-media/2', alt: 'Second image', caption: null, width: 800, height: 1200 },
    { url: '/en/managed-media/3', alt: 'Third image', caption: 'Third caption', width: 1000, height: 1000 },
];

describe('gallery lightbox', () => {
    it('renders an accessible dialog with the current image and counter', () => {
        render(<GalleryLightbox images={images} index={0} onClose={vi.fn()} onIndexChange={vi.fn()} />);

        const dialog = screen.getByRole('dialog', { name: 'Image viewer' });
        expect(dialog).toHaveAttribute('aria-modal', 'true');
        expect(screen.getByRole('img', { name: 'First image' })).toBeInTheDocument();
        expect(screen.getByText('First caption')).toBeInTheDocument();
        expect(screen.getByText('1')).toBeInTheDocument();
    });

    it('navigates with the arrow buttons', () => {
        const onIndexChange = vi.fn();
        render(<GalleryLightbox images={images} index={0} onClose={vi.fn()} onIndexChange={onIndexChange} />);

        fireEvent.click(screen.getByRole('button', { name: 'Next image' }));
        expect(onIndexChange).toHaveBeenCalledWith(1);

        fireEvent.click(screen.getByRole('button', { name: 'Previous image' }));
        expect(onIndexChange).toHaveBeenCalledWith(2);
    });

    it('supports keyboard navigation and closing', () => {
        const onClose = vi.fn();
        const onIndexChange = vi.fn();
        render(<GalleryLightbox images={images} index={1} onClose={onClose} onIndexChange={onIndexChange} />);

        fireEvent.keyDown(window, { key: 'ArrowRight' });
        expect(onIndexChange).toHaveBeenCalledWith(2);

        fireEvent.keyDown(window, { key: 'ArrowLeft' });
        expect(onIndexChange).toHaveBeenCalledWith(0);

        fireEvent.keyDown(window, { key: 'Escape' });
        expect(onClose).toHaveBeenCalled();
    });

    it('jumps to a thumbnail and closes on backdrop click', () => {
        const onClose = vi.fn();
        const onIndexChange = vi.fn();
        const { container } = render(<GalleryLightbox images={images} index={0} onClose={onClose} onIndexChange={onIndexChange} />);

        fireEvent.click(screen.getByRole('button', { name: 'Second image (2 / 3)' }));
        expect(onIndexChange).toHaveBeenCalledWith(1);

        const backdrop = container.ownerDocument.querySelector('.gallery-lightbox');
        if (backdrop) {
            fireEvent.click(backdrop);
        }
        expect(onClose).toHaveBeenCalled();
    });
});
