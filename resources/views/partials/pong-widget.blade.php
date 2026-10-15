{{-- Floating Ask Pong AI chat widget, shared by every admin page (layouts/pcg). --}}
<style>
    #pong-widget { position: fixed; right: 24px; bottom: 24px; z-index: 60; }
    #pong-toggle { position: relative; z-index: 2; cursor: pointer; }
    #pong-panel { position: absolute; right: 0; bottom: 76px; z-index: 1; }
</style>

    <div id="pong-widget" class="fixed bottom-5 right-5 z-[100]">

        {{-- Floating Toggle Button --}}
        <button type="button" id="pong-toggle" aria-label="Open Ask Pong AI" aria-expanded="false"
            class="flex h-16 w-16 items-center justify-center rounded-full
               bg-[#0033a0] text-white shadow-2xl ring-4 ring-[#f4c542]/40
               transition duration-200 hover:scale-105 hover:bg-[#00246f]
               focus:outline-none focus:ring-4 focus:ring-[#f4c542]/70">
            <svg id="pong-toggle-icon" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8
                     8.5 8.5 0 0 1-7.6 4.7
                     8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7
                     a8.4 8.4 0 0 1-.9-3.8
                     8.5 8.5 0 0 1 4.7-7.6
                     8.4 8.4 0 0 1 3.8-.9h.5
                     a8.5 8.5 0 0 1 8 8z" />
            </svg>
        </button>

        {{-- Chat Panel --}}
        <section id="pong-panel" aria-label="Ask Pong AI chat" aria-hidden="true"
            class="absolute bottom-20 right-0 hidden
               flex-col overflow-hidden rounded-2xl
               border border-slate-200 bg-white
               shadow-2xl"
            style="width:min(380px, calc(100vw - 32px)); height:min(540px, calc(100dvh - 120px));">

            {{-- Header --}}
            <div
                class="flex shrink-0 items-center justify-between
                    bg-gradient-to-r from-[#00246f] to-[#0033a0]
                    border-b-2 border-[#f4c542] px-4 py-4 text-white">

                <div class="flex min-w-0 items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center
                            justify-center rounded-xl bg-white/15 text-[#f4c542]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 8V4H8" />
                            <rect width="16" height="12" x="4" y="8" rx="2" />
                            <path d="M2 14h2m16 0h2M9 13h.01M15 13h.01M9 17h6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="truncate text-base font-bold">
                            Ask Pong AI
                        </h3>
                        <p class="text-xs text-white/75">
                            <span
                                class="mr-1 inline-block h-2 w-2
                                     rounded-full bg-green-400"></span>
                            AI assistant
                        </p>
                    </div>
                </div>

                <button type="button" id="pong-close" aria-label="Close chat"
                    class="ml-3 rounded-lg p-2 text-white/90
                       transition hover:bg-white/15 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="m18 6-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Welcome Message --}}
            <div class="shrink-0 border-b border-slate-100
                    bg-[#eef2f8] px-4 py-3">
                <p class="text-sm leading-5 text-slate-700">
                    Hello! I'm Pong AI. Type your question or tap the
                    microphone to speak.
                </p>
            </div>

            {{-- Messages --}}
            <div id="pong-messages" role="log" aria-live="polite" aria-relevant="additions"
                class="flex-1 space-y-4 overflow-y-auto
                   bg-slate-50 p-4"></div>

            {{-- Listening / Status --}}
            <div id="pong-status" role="status" aria-live="polite"
                class="hidden shrink-0 border-t border-slate-100
                   bg-white px-4 py-2 text-xs text-[#0033a0]">
            </div>

            {{-- Message Form --}}
            <form id="pong-form" class="shrink-0 border-t border-slate-200 bg-white p-3">
                <div class="flex items-end gap-2">

                    <textarea id="pong-input" name="message" rows="1" maxlength="4000" placeholder="Ask Pong AI..."
                        aria-label="Your message"
                        class="max-h-28 min-w-0 flex-1 resize-none
                           rounded-xl border border-slate-300
                           bg-white px-3 py-3 text-sm text-slate-900
                           outline-none placeholder:text-slate-400
                           focus:border-[#0033a0] focus:ring-2
                           focus:ring-[#0033a0]/15"
                        required></textarea>

                    {{-- Microphone --}}
                    <button type="button" id="pong-mic" title="Speak your message" aria-label="Start voice input"
                        aria-pressed="false"
                        class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl bg-slate-100
                           text-slate-700 transition
                           hover:bg-[#e8f0fe] hover:text-[#0033a0]
                           focus:outline-none focus:ring-2
                           focus:ring-[#f4c542]/70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10a7 7 0 0 0 14 0" />
                            <path d="M12 19v3M8 22h8" />
                        </svg>
                    </button>

                    {{-- Send --}}
                    <button type="submit" id="pong-send" aria-label="Send message" title="Send message"
                        class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl bg-[#0033a0]
                           text-white transition hover:bg-[#00246f]
                           disabled:cursor-not-allowed
                           disabled:opacity-50
                           focus:outline-none focus:ring-2
                           focus:ring-[#f4c542]/70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                    </button>

                </div>

                <p class="mt-2 text-center text-[10px] text-slate-400">
                    Enter to send · Shift + Enter for a new line
                </p>
            </form>
        </section>
    </div>

    {{-- Floating Chat JavaScript --}}
    <script>
        (() => {
            'use strict';

            // Prevent duplicate initialization if this script is included twice.
            if (window.__pongFloatingChatInitialized) return;
            window.__pongFloatingChatInitialized = true;

            function initializePongChat() {
                const widget = document.getElementById('pong-widget');
                const toggle = document.getElementById('pong-toggle');
                const panel = document.getElementById('pong-panel');
                const close = document.getElementById('pong-close');
                const messages = document.getElementById('pong-messages');
                const form = document.getElementById('pong-form');
                const input = document.getElementById('pong-input');
                const send = document.getElementById('pong-send');
                const mic = document.getElementById('pong-mic');
                const status = document.getElementById('pong-status');
                const toggleIcon = document.getElementById('pong-toggle-icon');

                if (
                    !widget || !toggle || !panel || !close ||
                    !messages || !form || !input || !send ||
                    !mic || !status
                ) {
                    console.error(
                        'Ask Pong AI initialization failed: a required element is missing.'
                    );
                    return;
                }

                // Use the existing Laravel named route.
                const chatUrl = @json(route('ai-chat.send'));
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content || '';

                let isSending = false;
                let recognition = null;
                let isListening = false;
                let finalTranscript = '';

                function setStatus(message, visible = true) {
                    status.textContent = message;
                    status.classList.toggle('hidden', !visible);
                }

                function openChat() {
                    panel.classList.remove('hidden');
                    panel.classList.add('flex');
                    panel.setAttribute('aria-hidden', 'false');
                    toggle.setAttribute('aria-expanded', 'true');

                    toggleIcon.innerHTML =
                        '<path d="m18 6-12 12M6 6l12 12"/>';

                    input.focus();
                }

                function closeChat() {
                    // Release the microphone when the panel closes.
                    if (recognition && isListening) {
                        recognition.stop();
                    }

                    panel.classList.add('hidden');
                    panel.classList.remove('flex');
                    panel.setAttribute('aria-hidden', 'true');
                    toggle.setAttribute('aria-expanded', 'false');

                    toggleIcon.innerHTML =
                        '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 ' +
                        ' 8.5 8.5 0 0 1-7.6 4.7 ' +
                        ' 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7 ' +
                        ' a8.4 8.4 0 0 1-.9-3.8 ' +
                        ' 8.5 8.5 0 0 1 4.7-7.6 ' +
                        ' 8.4 8.4 0 0 1 3.8-.9h.5 ' +
                        ' a8.5 8.5 0 0 1 8 8z"/>';
                }

                toggle.addEventListener('click', () => {
                    if (panel.classList.contains('hidden')) {
                        openChat();
                    } else {
                        closeChat();
                    }
                });

                close.addEventListener('click', closeChat);

                // Build message bubbles safely with textContent.
                function addMessage(text, sender) {
                    const row = document.createElement('div');
                    row.className = sender === 'user' ?
                        'flex justify-end' :
                        'flex justify-start';

                    const bubble = document.createElement('div');
                    bubble.className = sender === 'user' ?
                        'max-w-[85%] whitespace-pre-wrap break-words rounded-2xl rounded-br-md bg-[#0033a0] px-4 py-3 text-sm leading-6 text-white shadow-sm' :
                        'max-w-[90%] whitespace-pre-wrap break-words rounded-2xl rounded-bl-md border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700 shadow-sm';

                    bubble.textContent = text;
                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;

                    return row;
                }

                function addTypingIndicator() {
                    const row = document.createElement('div');
                    row.className = 'flex justify-start';

                    const bubble = document.createElement('div');
                    bubble.className =
                        'rounded-2xl rounded-bl-md border border-slate-200 ' +
                        'bg-white px-4 py-3 text-sm text-slate-500';

                    bubble.textContent = 'Pong AI is thinking...';
                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;

                    return row;
                }

                function extractAnswer(data) {
                    if (typeof data === 'string') return data;

                    if (!data || typeof data !== 'object') return '';

                    const candidates = [
                        data.answer,
                        data.reply,
                        data.response,
                        data.message,
                        data.data?.answer,
                        data.data?.reply,
                        data.data?.response,
                        data.result?.answer
                    ];

                    for (const candidate of candidates) {
                        if (typeof candidate === 'string' && candidate.trim()) {
                            return candidate.trim();
                        }
                    }

                    return '';
                }

                async function sendMessage(message) {
                    const trimmedMessage = message.trim();

                    if (!trimmedMessage || isSending) return;

                    addMessage(trimmedMessage, 'user');
                    input.value = '';
                    input.style.height = 'auto';

                    isSending = true;
                    send.disabled = true;
                    setStatus('Connecting to Pong AI...');

                    const typing = addTypingIndicator();

                    try {
                        const response = await fetch(chatUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                message: trimmedMessage
                            })
                        });

                        const rawResponse = await response.text();
                        let data;

                        try {
                            data = JSON.parse(rawResponse);
                        } catch {
                            throw new Error(
                                'The server returned an unexpected response. ' +
                                'Check your Laravel chat controller.'
                            );
                        }

                        if (!response.ok) {
                            throw new Error(
                                data.message ||
                                data.error ||
                                `Request failed (${response.status}).`
                            );
                        }

                        const answer = extractAnswer(data);

                        if (!answer) {
                            console.error('Unexpected Pong AI response:', data);
                            throw new Error(
                                'Pong AI returned no answer. Check the JSON ' +
                                'response in your Laravel chat controller.'
                            );
                        }

                        typing.remove();
                        addMessage(answer, 'assistant');
                        setStatus('', false);

                    } catch (error) {
                        typing.remove();
                        console.error('Pong AI chat error:', error);

                        addMessage(
                            'Sorry, I could not get a response. ' +
                            error.message,
                            'assistant'
                        );

                        setStatus(
                            'Unable to complete the request. Please try again.'
                        );
                    } finally {
                        isSending = false;
                        send.disabled = false;
                        input.focus();
                    }
                }

                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    sendMessage(input.value);
                });

                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        form.requestSubmit();
                    }
                });

                input.addEventListener('input', () => {
                    input.style.height = 'auto';
                    input.style.height =
                        Math.min(input.scrollHeight, 112) + 'px';
                });

                // Voice input: speech is transcribed into the message field.
                const SpeechRecognition =
                    window.SpeechRecognition ||
                    window.webkitSpeechRecognition;

                function resetMicButton() {
                    isListening = false;
                    mic.setAttribute('aria-pressed', 'false');
                    mic.setAttribute('aria-label', 'Start voice input');
                    mic.title = 'Speak your message';

                    mic.classList.remove('bg-red-100', 'text-red-700');
                    mic.classList.add('bg-slate-100', 'text-slate-700');
                }

                if (!SpeechRecognition) {
                    mic.addEventListener('click', () => {
                        setStatus(
                            'Voice input is not supported here. Please use ' +
                            'Google Chrome or Microsoft Edge.'
                        );
                    });
                } else {
                    recognition = new SpeechRecognition();
                    recognition.lang = 'en-US';
                    recognition.continuous = false;
                    recognition.interimResults = true;
                    recognition.maxAlternatives = 1;

                    mic.addEventListener('click', () => {
                        if (isListening) {
                            recognition.stop();
                            return;
                        }

                        finalTranscript = '';

                        try {
                            recognition.start();
                        } catch (error) {
                            console.warn(
                                'Could not start speech recognition:', error
                            );
                            setStatus(
                                'Could not start the microphone. Please try again.'
                            );
                        }
                    });

                    recognition.onstart = () => {
                        isListening = true;
                        mic.setAttribute('aria-pressed', 'true');
                        mic.setAttribute('aria-label', 'Stop voice input');
                        mic.title = 'Listening... click to stop';

                        mic.classList.remove(
                            'bg-slate-100', 'text-slate-700'
                        );
                        mic.classList.add('bg-red-100', 'text-red-700');

                        setStatus('Listening... speak now.');
                    };

                    recognition.onresult = (event) => {
                        let interimTranscript = '';

                        for (
                            let i = event.resultIndex; i < event.results.length; i++
                        ) {
                            const transcript =
                                event.results[i][0].transcript;

                            if (event.results[i].isFinal) {
                                finalTranscript += transcript;
                            } else {
                                interimTranscript += transcript;
                            }
                        }

                        input.value =
                            (finalTranscript + ' ' + interimTranscript).trim();

                        input.dispatchEvent(
                            new Event('input', {
                                bubbles: true
                            })
                        );
                    };

                    recognition.onerror = (event) => {
                        console.warn(
                            'Speech recognition error:', event.error
                        );

                        if (event.error === 'not-allowed') {
                            setStatus(
                                'Microphone access was denied. Allow microphone ' +
                                'permission in your browser settings.'
                            );
                        } else if (event.error === 'no-speech') {
                            setStatus('No speech detected. Tap the microphone to try again.');
                        } else if (event.error !== 'aborted') {
                            setStatus(
                                'Voice input encountered an error. Please try again.'
                            );
                        }
                    };

                    recognition.onend = () => {
                        resetMicButton();

                        if (input.value.trim()) {
                            setStatus(
                                'Voice captured. Review your message, then press Send.'
                            );
                        } else {
                            setStatus('', false);
                        }
                    };
                }

                // Close with Escape.
                document.addEventListener('keydown', (event) => {
                    if (
                        event.key === 'Escape' &&
                        !panel.classList.contains('hidden')
                    ) {
                        closeChat();
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializePongChat, {
                        once: true
                    }
                );
            } else {
                initializePongChat();
            }
        })();
    </script>
