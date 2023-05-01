@extends('vendor.installer.layouts.master')
@section('template_title')
    Step 3 | Purchase Info
@endsection

@section('title')
    <i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i>
    Purchase Info
@endsection

@section('container')

            <form method="POST" action="{{ route('LaravelInstaller::purchaseSaveWizard') }}">
                {{ csrf_field() }}
                <div class="form-group {{ $errors->has('purchase_code') ? ' has-error ' : '' }}">
                    <label for="purchase_code">
                        Please find the purchase code in your codecanyon download section.
                    </label>
                    <input type="text" name="purchase_code" id="purchase_code" value="{{ old('purchase_code') }}"
                           placeholder="Input your purchase code"/>
                    @if ($errors->has('purchase_code'))
                        <span class="error-block">
                            {{ $errors->first('purchase_code') }}
                    </span>
                    @endif
                    @if (session('status'))
                        <span class="has-error error-block">
                            {{ session('status') }}
                    </span>
                    @endif
                </div>

                <div class="buttons">
                    <button type="submit" class="button">
                        Configure Admin Info
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

@endsection

