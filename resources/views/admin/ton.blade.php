<div class="row g-3">
    <div class="mb-3 col-md-6">
        <label class="form-label" for="keyInput">{{ trans('ton::messages.price') }}</label>
        <input type="text" class="form-control @error('price') is-invalid @enderror" id="keyInput" name="price" value="{{ old('price', $gateway->data['price'] ?? '') }}" required>

        @error('price')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="keyInput">{{ trans('ton::messages.address') }}</label>
        <input type="text" class="form-control @error('address') is-invalid @enderror" id="keyInput" name="address" value="{{ old('address', $gateway->data['address'] ?? '') }}" required>

        @error('address')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="keyInput">{{ trans('ton::messages.color') }}</label>
        <select class="form-control @error('color') is-invalid @enderror" id="keyInput" name="color" required> 
            <option @if(old('color', $gateway->data['color'] ?? '') == "1") selected @endif value="1">{{ trans('ton::messages.white') }}</option> 
            <option @if(old('color', $gateway->data['color'] ?? '') == "2") selected @endif value="2">{{ trans('ton::messages.black') }}</option> 
        </select>

        @error('color')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="alert alert-info">
    <p>
        <i class="bi bi-info-circle"></i>
        @lang('ton::messages.setup')
    </p>
</div>
<div class="alert alert-info">
    <p>
        <i class="bi bi-info-circle"></i>
        @lang('ton::messages.setup2')
    </p>
</div>
