<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Preview - {{ $invitation->title }}</title>
    
    {{-- Live Edit Script - load first --}}
    <script src="{{ asset('assets/js/live-edit.js') }}"></script>
</head>
<body 
    data-invitation-id="{{ $invitation->id }}"
    data-is-owner="true"
>
    <script>
        // Immediately inject template
        (function() {
            const templateHTML = {!! json_encode($templateContent) !!};
            
            // Parse HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(templateHTML, 'text/html');
            
            // Inject head elements
            Array.from(doc.head.children).forEach(el => {
                if (el.tagName === 'META') {
                    const name = el.getAttribute('name');
                    const charset = el.getAttribute('charset');
                    if (name === 'csrf-token' || name === 'viewport' || charset) return;
                }
                if (el.tagName === 'TITLE') return;
                document.head.appendChild(el.cloneNode(true));
            });
            
            // Inject body content immediately
            document.write(doc.body.innerHTML);
            
            // Queue scripts to run after document is ready
            window.addEventListener('load', function() {
                const scripts = doc.body.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    if (oldScript.src) {
                        // External script
                        const script = document.createElement('script');
                        script.src = oldScript.src;
                        Array.from(oldScript.attributes).forEach(attr => {
                            if (attr.name !== 'src') {
                                script.setAttribute(attr.name, attr.value);
                            }
                        });
                        document.body.appendChild(script);
                    } else {
                        // Inline script - execute immediately
                        try {
                            eval(oldScript.textContent);
                        } catch(e) {
                            console.error('Script execution error:', e);
                        }
                    }
                });
            });
        })();
    </script>
</body>
</html>
