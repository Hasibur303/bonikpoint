<div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
    <label for="website">Leave this field empty</label>
    <input id="website" name="website" type="text" value="" tabindex="-1" autocomplete="off">
</div>

@if(config('turnstile.enabled'))
    <div class="mt-5">
        <div class="cf-turnstile" data-sitekey="{{ config('turnstile.site_key') }}"></div>
    </div>

    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
@endif

<x-input-error :messages="$errors->get('bot_protection')" class="mt-3" />
