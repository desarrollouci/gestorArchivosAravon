<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input name="{{ $name }}" value="{!! $value !!}" type="{{ $type }}" class="form-control @error('{{ $name }}') is-invalid @enderror" id="{{ $name }}" aria-describedby="{{ $name }}_help">
    @if ($help)
        <small id="{{ $name }}_help" class="form-text text-muted">{{ $help }}</small>
    @endif
    @error('{{$name}}')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>