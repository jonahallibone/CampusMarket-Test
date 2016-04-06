@extends('layouts.master')

@section('content')
<section id="profile-container">
	<div class="wrapper">
		<div id="left-column">
			<div class="large-profile">
				<div id="profile-l-image">
					<!--Plceholder for the *profile image* -->

				</div>
			</div>
		</div>
		<div id="right-column">
			<div class="right-user-information">
				<div class="input-wrapper">
					<label class="field-label">First name:</label>
					<input type="text" id="name-editable" class="text-input-editable" value="{{$firstname}}" />
				</div>
				<div class="input-wrapper">
					<label class="field-label">Last name:</label>
					<input type="text" id="name-editable" class="text-input-editable" value="{{$lastname}}" />
				</div>
				<div class="input-wrapper">
					<label class="field-label">Username:</label>
					<input type="text" id="name-editable" class="text-input-editable" value="{{$username}}" />
				</div>
				<div class="input-wrapper">
					<label class="field-label">Email:</label>
					<input type="text" id="name-editable" class="text-input-editable" value="{{$email}}" />
				</div>
			</div>
		</div>
	</div>
</section>
@stop
