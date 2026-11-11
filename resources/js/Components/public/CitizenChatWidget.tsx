import { useEffect, useRef, useState } from 'react';
import { usePublicLocale, usePublicTranslation } from '../../usePublicTranslation';

type ChatMessage = {
    id: string;
    role: 'user' | 'model';
    content: string;
    timestamp: Date;
    isError?: boolean;
};

export default function CitizenChatWidget() {
    const t = usePublicTranslation();
    const locale = usePublicLocale();

    const [isOpen, setIsOpen] = useState(false);
    const [input, setInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [messages, setMessages] = useState<ChatMessage[]>([]);

    const messagesEndRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLTextAreaElement>(null);

    // Initial greeting message when opened for the first time
    useEffect(() => {
        if (messages.length === 0) {
            setMessages([
                {
                    id: 'greeting',
                    role: 'model',
                    content: t('chatWelcomeMessage'),
                    timestamp: new Date(),
                },
            ]);
        }
    }, [t, messages.length]);

    // Auto-scroll chat log
    useEffect(() => {
        if (isOpen) {
            messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
    }, [messages, isOpen, isLoading]);

    // Focus input on modal open
    useEffect(() => {
        if (isOpen) {
            setTimeout(() => inputRef.current?.focus(), 150);
        }
    }, [isOpen]);

    // Handle Escape key to close
    useEffect(() => {
        const onKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen) {
                setIsOpen(false);
            }
        };
        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, [isOpen]);

    const sendMessage = async (textToSend?: string) => {
        const messageText = (textToSend || input).trim();
        if (!messageText || isLoading) return;

        const userMsg: ChatMessage = {
            id: `usr_${Date.now()}`,
            role: 'user',
            content: messageText,
            timestamp: new Date(),
        };

        const updatedHistory = [...messages, userMsg];
        setMessages(updatedHistory);
        setInput('');
        setIsLoading(true);

        try {
            // Format history for the backend (excluding greetings / errors)
            const historyPayload = updatedHistory
                .filter((m) => m.id !== 'greeting' && !m.isError)
                .slice(-8)
                .map((m) => ({
                    role: m.role,
                    content: m.content,
                }));

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify({
                    message: messageText,
                    history: historyPayload.slice(0, -1), // prior history excluding this prompt
                    locale,
                }),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                setMessages((prev) => [
                    ...prev,
                    {
                        id: `mod_${Date.now()}`,
                        role: 'model',
                        content: data.reply,
                        timestamp: new Date(),
                    },
                ]);
            } else {
                setMessages((prev) => [
                    ...prev,
                    {
                        id: `err_${Date.now()}`,
                        role: 'model',
                        content: data.reply || t('chatErrorRetry'),
                        timestamp: new Date(),
                        isError: true,
                    },
                ]);
            }
        } catch {
            setMessages((prev) => [
                ...prev,
                {
                    id: `err_${Date.now()}`,
                    role: 'model',
                    content: t('chatErrorRetry'),
                    timestamp: new Date(),
                    isError: true,
                },
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    };

    const quickPrompts = [
        { label: t('chatPromptRates'), text: t('chatPromptRates') },
        { label: t('chatPromptLicence'), text: t('chatPromptLicence') },
        { label: t('chatPromptFault'), text: t('chatPromptFault') },
        { label: t('chatPromptContacts'), text: t('chatPromptContacts') },
    ];

    return (
        <div className="citizen-chat-wrapper" aria-live="polite">
            {/* Floating Trigger Button */}
            {!isOpen && (
                <button
                    type="button"
                    className="citizen-chat-trigger"
                    onClick={() => setIsOpen(true)}
                    aria-label={t('chatOpenButton')}
                    title={t('chatOpenButton')}
                >
                    <span className="citizen-chat-trigger-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="w-6 h-6">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </span>
                    <span className="citizen-chat-trigger-text">{t('chatOpenButton')}</span>
                    <span className="citizen-chat-trigger-ping" aria-hidden="true" />
                </button>
            )}

            {/* Chat Dialog Window */}
            {isOpen && (
                <div
                    className="citizen-chat-window"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="citizen-chat-heading"
                >
                    {/* Header */}
                    <div className="citizen-chat-header">
                        <div className="citizen-chat-header-info">
                            <div className="citizen-chat-avatar" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </div>
                            <div>
                                <h2 id="citizen-chat-heading" className="citizen-chat-title">
                                    {t('chatAssistantTitle')}
                                </h2>
                                <p className="citizen-chat-subtitle">{t('chatAssistantSubtitle')}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            className="citizen-chat-close-btn"
                            onClick={() => setIsOpen(false)}
                            aria-label={t('chatCloseButton')}
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" className="w-5 h-5">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>

                    {/* Chat Messages Body */}
                    <div className="citizen-chat-body">
                        {messages.map((msg) => (
                            <div
                                key={msg.id}
                                className={`citizen-chat-message-row ${msg.role === 'user' ? 'citizen-chat-row-user' : 'citizen-chat-row-model'}`}
                            >
                                {msg.role === 'model' && (
                                    <div className="citizen-chat-msg-avatar" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="currentColor" className="w-3.5 h-3.5 text-civic-navy">
                                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                        </svg>
                                    </div>
                                )}
                                <div
                                    className={`citizen-chat-bubble ${msg.role === 'user' ? 'citizen-chat-bubble-user' : 'citizen-chat-bubble-model'} ${msg.isError ? 'citizen-chat-bubble-error' : ''}`}
                                >
                                    <div className="citizen-chat-bubble-content">
                                        {msg.content.split('\n').map((line, idx) => (
                                            <p key={idx} className={line.trim() === '' ? 'h-2' : ''}>
                                                {line}
                                            </p>
                                        ))}
                                    </div>
                                    <span className="citizen-chat-timestamp">
                                        {msg.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </span>
                                </div>
                            </div>
                        ))}

                        {/* Typing indicator */}
                        {isLoading && (
                            <div className="citizen-chat-message-row citizen-chat-row-model">
                                <div className="citizen-chat-msg-avatar" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="currentColor" className="w-3.5 h-3.5 text-civic-navy">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                    </svg>
                                </div>
                                <div className="citizen-chat-bubble citizen-chat-bubble-model citizen-chat-bubble-typing">
                                    <span className="citizen-chat-dot" />
                                    <span className="citizen-chat-dot" />
                                    <span className="citizen-chat-dot" />
                                    <span className="sr-only">{t('chatThinking')}</span>
                                </div>
                            </div>
                        )}

                        <div ref={messagesEndRef} />
                    </div>

                    {/* Quick suggestion chips */}
                    {messages.length <= 2 && !isLoading && (
                        <div className="citizen-chat-quick-section">
                            <span className="citizen-chat-quick-label">{t('chatQuickPromptsTitle')}:</span>
                            <div className="citizen-chat-quick-chips">
                                {quickPrompts.map((qp, idx) => (
                                    <button
                                        key={idx}
                                        type="button"
                                        className="citizen-chat-chip"
                                        onClick={() => sendMessage(qp.text)}
                                    >
                                        {qp.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Input footer */}
                    <div className="citizen-chat-footer">
                        <div className="citizen-chat-input-wrapper">
                            <textarea
                                ref={inputRef}
                                className="citizen-chat-input"
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                onKeyDown={handleKeyDown}
                                placeholder={t('chatInputPlaceholder')}
                                rows={1}
                                maxLength={1000}
                                disabled={isLoading}
                                aria-label={t('chatInputPlaceholder')}
                            />
                            <button
                                type="button"
                                className="citizen-chat-send-btn"
                                onClick={() => sendMessage()}
                                disabled={!input.trim() || isLoading}
                                aria-label={t('chatSendButton')}
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" className="w-5 h-5">
                                    <line x1="22" y1="2" x2="11" y2="13" />
                                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                </svg>
                            </button>
                        </div>
                        <p className="citizen-chat-disclaimer">
                            {t('chatDisclaimers')}
                        </p>
                    </div>
                </div>
            )}
        </div>
    );
}
