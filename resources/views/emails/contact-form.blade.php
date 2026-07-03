<x-mail::message>
# New contact form submission

You received a new message from your portfolio contact form.

**Name:** {{ $name }}

**Email:** {{ $email }}

**Project type:** {{ $projectType }}

**Message:**

{{ $message }}

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $name }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
