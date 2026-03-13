<?php
$languageCode = $module['language_code'] ?? 'en';
$content = array_replace_recursive(
    cms_contact_panel_default_content($languageCode),
    is_array($module['content'] ?? null) ? $module['content'] : []
);

$contacts = $content['contacts'] ?? [];
$nextSteps = $content['next_steps'] ?? [];
$locations = $content['locations'] ?? [];
$hours = $content['hours'] ?? [];
$serviceOptions = $content['service_options'] ?? [];
$moduleKeySlug = trim((string) preg_replace('/[^a-z0-9]+/i', '-', (string) ($module['module_key'] ?? 'contact-panel')), '-');
$formId = 'contact-form-' . ($moduleKeySlug ?: 'contact-panel');
$messageId = $formId . '-message';
$anchorId = 'contact-form';

$contactIcon = static function (string $kind): array {
    return match ($kind) {
        'whatsapp' => [
            'bg' => 'bg-green-100 group-hover:bg-green-200',
            'text' => 'text-green-600',
            'svg' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />',
            'fill' => 'currentColor',
        ],
        'email' => [
            'bg' => 'bg-primary/10 group-hover:bg-primary/20',
            'text' => 'text-primary',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            'fill' => 'none',
        ],
        default => [
            'bg' => 'bg-primary/10 group-hover:bg-primary/20',
            'text' => 'text-primary',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />',
            'fill' => 'none',
        ],
    };
};

$locationIcon = static function (string $kind): string {
    return $kind === 'facility'
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />';
};
?>
<section class="bg-white py-20" id="<?= cms_escape($anchorId) ?>">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-16 lg:grid-cols-[1.05fr_0.95fr]">
            <div>
                <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
                <?php if ($module['title']): ?><h2 class="mb-6 text-2xl font-bold text-navy"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
                <?php if ($module['subtitle']): ?><p class="mb-8 text-gray-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>

                <form id="<?= cms_escape($formId) ?>" class="space-y-6">
                    <div id="<?= cms_escape($messageId) ?>" class="hidden rounded-lg p-4 text-center font-medium"></div>
                    <div class="hidden" aria-hidden="true">
                        <label for="<?= cms_escape($formId) ?>-website">Website</label>
                        <input type="text" id="<?= cms_escape($formId) ?>-website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="<?= cms_escape($formId) ?>-firstName" class="mb-2 block text-sm font-medium text-navy">First Name *</label>
                            <input type="text" id="<?= cms_escape($formId) ?>-firstName" name="firstName" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="John">
                        </div>
                        <div>
                            <label for="<?= cms_escape($formId) ?>-lastName" class="mb-2 block text-sm font-medium text-navy">Last Name *</label>
                            <input type="text" id="<?= cms_escape($formId) ?>-lastName" name="lastName" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Smith">
                        </div>
                    </div>

                    <div>
                        <label for="<?= cms_escape($formId) ?>-email" class="mb-2 block text-sm font-medium text-navy">Email Address *</label>
                        <input type="email" id="<?= cms_escape($formId) ?>-email" name="email" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="john@dentalclinic.com">
                    </div>

                    <div>
                        <label for="<?= cms_escape($formId) ?>-phone" class="mb-2 block text-sm font-medium text-navy">Phone Number</label>
                        <input type="tel" id="<?= cms_escape($formId) ?>-phone" name="phone" class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="+852 XXXX XXXX">
                    </div>

                    <div>
                        <label for="<?= cms_escape($formId) ?>-clinic" class="mb-2 block text-sm font-medium text-navy">Clinic / Practice Name</label>
                        <input type="text" id="<?= cms_escape($formId) ?>-clinic" name="clinic" class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Your Dental Clinic">
                    </div>

                    <div>
                        <label for="<?= cms_escape($formId) ?>-service" class="mb-2 block text-sm font-medium text-navy">Service Interest</label>
                        <?php if ($serviceOptions !== []): ?>
                            <select id="<?= cms_escape($formId) ?>-service" name="service" class="w-full cursor-pointer rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20">
                                <?php foreach ($serviceOptions as $option): ?>
                                    <option value="<?= cms_escape($option['value'] ?? '') ?>"><?= cms_escape($option['label'] ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" id="<?= cms_escape($formId) ?>-service" name="service" class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Service">
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="<?= cms_escape($formId) ?>-message" class="mb-2 block text-sm font-medium text-navy">Message *</label>
                        <textarea id="<?= cms_escape($formId) ?>-message" name="message" rows="5" required class="w-full resize-none rounded-lg border border-gray-300 px-4 py-3 outline-none transition-colors duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Tell us about your case type, scanner platform, shipping questions, or onboarding needs..."></textarea>
                    </div>

                    <button type="submit" class="w-full cursor-pointer rounded-lg bg-primary py-4 text-lg font-semibold text-white transition-colors duration-200 hover:bg-primary-dark">
                        Send Message
                    </button>
                </form>
            </div>

            <div>
                <?php if (!empty($content['aside_title'])): ?><h2 class="mb-6 text-2xl font-bold text-navy"><?= cms_escape($content['aside_title']) ?></h2><?php endif; ?>
                <?php if (!empty($content['aside_intro'])): ?><p class="mb-8 text-gray-600"><?= cms_escape($content['aside_intro']) ?></p><?php endif; ?>

                <?php if ($contacts !== []): ?>
                    <div class="mb-10 space-y-6">
                        <?php foreach ($contacts as $item): ?>
                            <?php $icon = $contactIcon((string) ($item['kind'] ?? 'phone')); ?>
                            <a href="<?= cms_escape($item['href'] ?? '#') ?>" class="group flex items-center gap-4 rounded-xl bg-gray-50 p-4 transition-colors duration-200 hover:bg-gray-100"<?= (($item['kind'] ?? '') === 'whatsapp') ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl transition-colors duration-200 <?= cms_escape($icon['bg']) ?>">
                                    <svg class="h-6 w-6 <?= cms_escape($icon['text']) ?>" fill="<?= cms_escape($icon['fill']) ?>" stroke="currentColor" viewBox="0 0 24 24">
                                        <?= $icon['svg'] ?>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500"><?= cms_escape($item['label'] ?? '') ?></p>
                                    <p class="font-semibold text-navy"><?= cms_escape($item['value'] ?? '') ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($nextSteps !== []): ?>
                    <div class="mb-10 grid gap-4 sm:grid-cols-2">
                        <?php foreach ($nextSteps as $item): ?>
                            <?php $isPrimary = ($item['tone'] ?? 'primary') === 'primary'; ?>
                            <a href="<?= cms_escape(cms_localized_href($item['href'] ?? '#', $languageCode)) ?>" class="<?= $isPrimary ? 'bg-navy text-white hover:bg-navy-light' : 'border border-gray-200 bg-gray-50 text-navy hover:border-primary' ?> rounded-2xl p-6 transition-colors">
                                <?php if (!empty($item['eyebrow'])): ?><p class="mb-3 text-xs uppercase tracking-[0.2em] <?= $isPrimary ? 'text-white/60' : 'text-primary' ?>"><?= cms_escape($item['eyebrow']) ?></p><?php endif; ?>
                                <?php if (!empty($item['title'])): ?><h3 class="mb-2 text-xl font-bold"><?= cms_escape($item['title']) ?></h3><?php endif; ?>
                                <?php if (!empty($item['text'])): ?><p class="text-sm <?= $isPrimary ? 'text-white/80' : 'text-gray-600' ?>"><?= cms_escape($item['text']) ?></p><?php endif; ?>
                                <?php if (!empty($item['cta'])): ?><p class="mt-4 text-sm font-semibold <?= $isPrimary ? 'text-accent' : 'text-primary' ?>"><?= cms_escape($item['cta']) ?></p><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($content['locations_title'])): ?><h3 class="mb-4 text-xl font-bold text-navy"><?= cms_escape($content['locations_title']) ?></h3><?php endif; ?>
                <?php if ($locations !== []): ?>
                    <div class="space-y-6">
                        <?php foreach ($locations as $item): ?>
                            <div class="rounded-xl bg-gray-50 p-6">
                                <div class="mb-3 flex items-center gap-3">
                                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?= $locationIcon((string) ($item['kind'] ?? 'location')) ?>
                                    </svg>
                                    <h4 class="font-semibold text-navy"><?= cms_escape($item['title'] ?? '') ?></h4>
                                </div>
                                <div class="ml-8 text-gray-600"><?= $item['body_html'] ?? '' ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($content['hours_title']) || $hours !== []): ?>
                    <div class="mt-8 rounded-xl border border-primary/10 bg-primary/5 p-6">
                        <?php if (!empty($content['hours_title'])): ?><h4 class="mb-3 font-semibold text-navy"><?= cms_escape($content['hours_title']) ?></h4><?php endif; ?>
                        <?php if ($hours !== []): ?>
                            <div class="space-y-2 text-gray-600">
                                <?php foreach ($hours as $item): ?>
                                    <div class="flex justify-between gap-4">
                                        <span><?= cms_escape($item['label'] ?? '') ?></span>
                                        <span class="font-medium"><?= cms_escape($item['value'] ?? '') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($content['hours_note'])): ?><p class="mt-4 text-sm text-gray-500"><?= cms_escape($content['hours_note']) ?></p><?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script data-cfasync="false">
        (function () {
            const form = document.getElementById(<?= json_encode($formId) ?>);
            const messageBox = document.getElementById(<?= json_encode($messageId) ?>);

            if (!form || !messageBox) {
                return;
            }

            function showFormMessage(type, text) {
                messageBox.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
                messageBox.classList.add(type === 'success' ? 'bg-green-100' : 'bg-red-100');
                messageBox.classList.add(type === 'success' ? 'text-green-700' : 'text-red-700');
                messageBox.textContent = text;
            }

            function normalizeField(value) {
                return typeof value === 'string' ? value.trim() : '';
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;

                submitButton.disabled = true;
                submitButton.textContent = 'Sending...';
                messageBox.classList.add('hidden');

                const formData = {
                    firstName: normalizeField(form.firstName.value),
                    lastName: normalizeField(form.lastName.value),
                    email: normalizeField(form.email.value),
                    phone: normalizeField(form.phone.value),
                    clinic: normalizeField(form.clinic.value),
                    service: normalizeField(form.service.value),
                    message: normalizeField(form.message.value),
                    website: normalizeField(form.website.value)
                };

                if (!formData.firstName || !formData.lastName || !formData.email || !formData.message) {
                    showFormMessage('error', 'Please complete all required fields.');
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                    return;
                }

                try {
                    const response = await fetch('/cms/api/contact-submit.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        showFormMessage('success', result.message);
                        form.reset();
                    } else {
                        showFormMessage('error', result.error || 'Something went wrong. Please try again.');
                    }
                } catch (error) {
                    showFormMessage('error', 'Network error. Please check your connection.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                    messageBox.classList.remove('hidden');
                }
            });
        }());
    </script>
</section>
