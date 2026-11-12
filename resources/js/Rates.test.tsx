import { fireEvent, render, screen } from '@testing-library/react';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ children, href }: { children: ReactNode; href: string }) => <a href={href}>{children}</a>,
}));
vi.mock('./Layouts/PublicLayout', () => ({ default: ({ children }: { children: ReactNode }) => <main>{children}</main> }));
vi.mock('./usePublicTranslation', () => ({
    usePublicLocale: () => 'en',
    usePublicTranslation: () => (key: string) => key,
}));

import Rates from './Pages/Rates';

describe('rates bank accounts', () => {
    it('removes withdrawn accounts without restoring sample accounts', () => {
        const view = render(<Rates page={null} schedules={[]} bankAccounts={[{
            id: 1, bank_name: 'Published Council Bank', account_name: 'Council',
            account_number: '123456', branch_name: 'Mutoko', branch_code: null,
            currency: 'ZiG', category: 'rates', purpose: 'Rates payments',
        }]} />);
        expect(screen.getByText('Published Council Bank')).toBeInTheDocument();

        view.rerender(<Rates page={null} schedules={[]} bankAccounts={[]} />);
        expect(screen.queryByText('Published Council Bank')).not.toBeInTheDocument();
        expect(screen.queryByText('CBZ Bank')).not.toBeInTheDocument();
        expect(screen.queryByText('02923984010012')).not.toBeInTheDocument();
        expect(screen.getByRole('status')).toHaveTextContent('No published bank accounts');
    });
    it('renders managed mobile instructions and copies the current code without fallback content', async () => {
        const writeText = vi.fn().mockResolvedValue(undefined);
        Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText } });
        vi.stubGlobal('isSecureContext', true);
        const view = render(<Rates page={null} schedules={[]} mobilePayments={[{
            id: 9, account_name: 'Council Mobile Payment', account_number: '00123',
            code_label: 'Biller Code', instructions: 'Dial *123#.\nEnter your reference.',
        }]} />);
        expect(screen.getByText('Council Mobile Payment')).toBeInTheDocument();
        expect(screen.getByText('Dial *123#.')).toBeInTheDocument();
        expect(screen.getByText('Enter your reference.')).toBeInTheDocument();
        fireEvent.click(screen.getByRole('button', { name: 'copyCode' }));
        expect(writeText).toHaveBeenCalledWith('00123');
        await screen.findByRole('button', { name: 'copiedTick' });

        view.rerender(<Rates page={null} schedules={[]} mobilePayments={[]} />);
        expect(screen.queryByText('Council Mobile Payment')).not.toBeInTheDocument();
        expect(screen.queryByText('94812')).not.toBeInTheDocument();
        expect(screen.queryByText('48920')).not.toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'copyCode' })).not.toBeInTheDocument();
        vi.unstubAllGlobals();
    });

});
