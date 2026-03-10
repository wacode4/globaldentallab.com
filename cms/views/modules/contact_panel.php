<?php $content = $module['content']; $items = $content['items'] ?? []; ?>
<section class="bg-white py-20">
    <div class="mx-auto grid max-w-7xl gap-16 px-6 lg:grid-cols-[1.05fr_0.95fr]">
        <div>
            <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
            <?php if ($module['title']): ?><h2 class="mb-4 text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
            <?php if ($module['subtitle']): ?><p class="mb-8 text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
            <form id="contact-form" class="space-y-5 rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <div id="cmsContactMessage" class="hidden rounded-xl px-4 py-3 text-sm font-semibold"></div>
                <div class="grid gap-5 md:grid-cols-2">
                    <input class="rounded-xl border border-slate-300 px-4 py-3" name="firstName" placeholder="First Name" required>
                    <input class="rounded-xl border border-slate-300 px-4 py-3" name="lastName" placeholder="Last Name" required>
                </div>
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="email" placeholder="Email" required>
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="phone" placeholder="Phone">
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="clinic" placeholder="Clinic">
                <input class="hidden" aria-hidden="true" tabindex="-1" autocomplete="off" name="website">
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="service" placeholder="Service">
                <textarea class="min-h-36 w-full rounded-xl border border-slate-300 px-4 py-3" name="message" placeholder="Message" required></textarea>
                <button class="rounded-xl bg-navy px-6 py-3 font-bold text-white" type="submit">Send Message</button>
            </form>
        </div>
        <div>
            <div class="rounded-3xl bg-slate-50 p-8">
                <?php foreach ($items as $item): ?>
                    <div class="mb-6 last:mb-0">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($item['label'] ?? '') ?></p>
                        <div class="mt-2 text-lg font-semibold text-navy"><?= $item['value_html'] ?? cms_escape($item['value'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('contact-form')?.addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = event.currentTarget;
            const messageBox = document.getElementById('cmsContactMessage');
            const submitButton = form.querySelector('button[type="submit"]');
            const formData = Object.fromEntries(new FormData(form).entries());
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

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
                messageBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold ' + (result.success ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700');
                messageBox.textContent = result.success ? result.message : (result.error || 'Unable to submit.');
                messageBox.classList.remove('hidden');
                if (result.success) {
                    form.reset();
                }
            } catch (error) {
                messageBox.className = 'rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700';
                messageBox.textContent = 'Unable to submit right now.';
                messageBox.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    </script>
</section>
