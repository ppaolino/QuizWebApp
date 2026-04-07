@props(['type' => 'players']) <!-- default to players -->

<div class="col-12 col-lg-6 mx-auto">
    <input type="text" id="{{ $type }}-search" class="form-control typeahead" placeholder="Cerca...">
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let type = "{{ $type }}";
        let endpoint = `/${type}/search?input=%QUERY`;
        let selectedValue = null;

        let engine = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: endpoint,
                wildcard: '%QUERY',
                transform: function(response) {
                    return response.slice(0, 10); // ensure only 10 shown
                }
            }
        });

        let typeaheadInput = $(`#${type}-search`);

        typeaheadInput.typeahead({
            hint: true,
            highlight: true,
            minLength: 2
        }, {
            name: type,
            display: function(item) {
                return item.name;
            },
            source: engine,
            limit: 10,
            templates: {
                suggestion: function(data) {
                    @if ($type === 'players')
                        return `
                        <div class="tt-suggestion d-flex align-items-center justify-content-between">
                            <span class="search-name">${data.name}</span>
                            <span class="search-extra">${data.position}</span>
                        </div>`;
                    @else
                        return `
                        <div class="tt-suggestion d-flex align-items-center justify-content-between">
                            <span class="search-name">${data.name}</span>
                        </div>`;
                    @endif
                },
                notFound: function() {
                    return `<div class="tt-suggestion text-muted">Nessun risultato trovato</div>`;
                }
            }
        });

        // Treat both explicit select and autocomplete as valid selection.
        typeaheadInput.bind('typeahead:select typeahead:autocomplete', function(ev, suggestion) {
            // Force visible text immediately; avoids first-click UI lag in modal contexts.
            $(this).typeahead('val', suggestion.name);
            $(this).data('selectedValue', suggestion.name);
            $(this).data('data-selected-value', suggestion.id);
            $(this).typeahead('close');
        });

        // When input loses focus
        typeaheadInput.on('blur', function() {
            const $input = $(this);

            // On mouse click selection, blur can fire before typeahead updates selection.
            // Delay cleanup to avoid dropping the first click.
            setTimeout(function() {
                if ($input.data('selectedValue') !== $input.val()) {
                    //$input.val('');
                    $input.data('selectedValue', null);
                    $input.data('data-selected-value', null);
                }
            }, 120);
        });

        // When input changes (keyup, delete, etc.)
        typeaheadInput.on('input', function() {
            if ($(this).data('selectedValue') && $(this).val() !== $(this).data('selectedValue')) {
                $(this).data('selectedValue', null);
                $(this).data('data-selected-value', null);
                // Clear immediately or wait for blur - uncomment next line for immediate clear
                // $(this).val('');
            }
        });

        // Prevent form submission on enter
        typeaheadInput.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if ($(this).data('selectedValue') !== $(this).val()) {
                    $(this).val('');
                    $(this).data('selectedValue', null);
                    $(this).data('data-selected-value', null);
                }
            }
        });
    });
</script>
@endpush
