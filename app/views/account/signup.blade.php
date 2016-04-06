@extends('layouts.master')

@section('content')
<section id="main-signup">
      <div class="signup-form-container">
        <span class="about-title">
          <h1 class="about-us-title">Sign up for BlackMarket <span class="green-u">U</span></h1>
        </span>
        <form id="signup-page-form" action="{{ URL::route('account-create-post') }}" method="post">

        <div class="input-container">
            <label for="firstname">First Name</label>
            @if($errors->has('first-name'))
            <input type="text" id="first-name" name="first-name" placeholder="Johnny" style="border-color:#F00;">
            <span class="form-error">{{$errors->first('first-name');}}</span>
            @else
            <input type="text" name="first-name" placeholder="Johnny" {{ (Input::old('first-name')) ? ' value="' . e(Input::old('first-name')).'"' : ''}}>
            @endif
        </div>
        <div class="input-container">
          <label for="last-name">Last Name</label>
          @if($errors->has('last-name'))
          <input type="text" name="last-name" placeholder="Appleseed" style="border:1px #F00 solid">
          <span class="form-error">{{$errors->first('last-name');}}</span>
          @else
          <input type="text" name="last-name" placeholder="Appleseed" {{ (Input::old('last-name')) ? ' value="' . e(Input::old('last-name')).'"' : ''}}>
          @endif
        </div>
        <div class="input-container">
          <label for="first-name">Username</label>
          @if($errors->has('username'))
          <input type="text" name="username" placeholder="johnnyappleseed" style="border:1px #F00 solid">
          <span class="form-error">{{$errors->first('username');}}</span>
          @else
          <input type="text" name="username" placeholder="johnnyappleseed" {{ (Input::old('last-name')) ? ' value="' . e(Input::old('username')).'"' : ''}}>
          @endif
        </div>
        <div class="input-container">
          <label for="email">Email (Requires .edu)</label>
          @if($errors->has('email'))
          <input type="text" name="email" placeholder="johnny@appleseed.edu" style="border:1px #F00 solid">
          <span class="form-error">{{$errors->first('email');}}</span>
          @elseif(isset($fpemail))
          <input type="text" name="email" placeholder="johnny@appleseed.edu" value="{{ $fpemail }}">
          @else
          <input type="text" name="email" placeholder="johnny@appleseed.edu" {{ (Input::old('email')) ? ' value="' . e(Input::old('email')).'"' : ''}}>
          @endif
        </div>
        <div class="input-container">
          <label for="city">City (optional)</label>
          <input type="text" name="city" placeholder="Where is your college?">
        </div>
        <div class="input-container">
          <label for="state">State (optional)</label>
          <input type="text" name="state" placeholder="What state are you in?">
        </div>
        <div class="input-container">
          <label for="password">Password (Twice)</label>
          @if($errors->has('password-init'))
          <input type="password" name="password-init" placeholder="Password" style="border:solid 1px #F00">
          <span class="form-error">{{$errors->first('password-init');}}</span>
          @else
          <input type="password" name="password-init" placeholder="Password">
          @endif
        </div>
        <div class="input-container">
          @if($errors->has('password-check'))
          <input type="password" name="password-check" placeholder="Password Again" style="border:solid 1px #F00">
          <span class="form-error">{{$errors->first('password-check');}}</span>
          @else
          <input type="password" name="password-check" placeholder="Password Again">
          @endif
        </div>
        <div class="input-container">
          <div class="agreement">
            <input type="checkbox" value="None" id="agreement" name="check" />
            <label for="agreement"></label>
          </div>
          <span class="agree-text">I have read and agree to the BlackMarket U Terms and Conditions</span>
        </div>
        <div class="input-container">
          <input type="submit" value="Sign up" class="signup-submit-button">
        </div>
      {{ Form::token() }}
    </form>
  </div>
</section>
@stop