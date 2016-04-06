@extends('layouts.master')


@section('content')
<section id="main-login">
      <div class="login-frame">
        <div class="row-top">
          <h2 id="login-title">BlackMarket U - Login</h2>
        </div>
        <div class="bottom-container">
          <div class="field-wrapper">
            <form id="login-page-form" action="{{ URL::route('account-login-post') }}" method="post">
              @if($errors->has('email'))
              <input type="text" class="the-username" name="email" placeholder="Email ending in .edu" style="border:#F00 1px solid;">
              <!--<span class="form-error">{{ $errors->first('email'); }}</span>-->
              @else
              <input type="text" class="the-username" name="email" placeholder="Email ending in .edu" {{ (Input::old('email')) ? ' value="' . e(Input::old('email')).'"' : ''}}>
              @endif
              @if($errors->has('password'))
              <input type="password" class="the-password" name="password" placeholder="Password" style="border:#F00 1px solid;">
              <!--<span class="form-error">{{ $errors->first('password'); }}</span>-->
                  @else
                  <input type="password" class="the-password" name="password" placeholder="Password">
                  @endif
              <div class="remember-me">
                <input type="checkbox" value="None" id="remember-me" name="remember-me" />
                <label for="remember-me"></label>
              </div>
              <span class="remember-text">Remember me</span>
              <input type="submit" class="login-submit-button" value="Login">
              <a href="#" class="password-recovery-link">Forgot Username or Password?</a>
              {{ Form::token() }}
            </form>
          </div>
        </div>
      </div>
    </section>
@stop