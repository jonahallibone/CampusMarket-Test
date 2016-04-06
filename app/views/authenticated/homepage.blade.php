@extends('layouts.master')

@section('content')
<section id="logged-in-feed">
	<div class="column left-column">
		<div id="left-column-content">
				<nav id="menu-bar">
					<span class="menu-title">Categories</span>
					<ul id="left-side-menu">
						@foreach($categories as $category)
							<li class="left-menu-item"><a href="javascript:void(0)" class="cat-link">{{ $category->title }}&nbsp;({{$category->count}}) </a></li>
						@endforeach
					</ul>
				</nav>
		</div>
	</div>
	<div class="column middle-column">
		<div class="new-post-top ">
			<form id="new-post-form" class="dropzone" method="POST" action="{{ URL::route('post-create-item') }}" enctype="multipart/form-data" >
				<div class="input-container">
					<input type="text" name="item-title" id="item-title" placeholder="Title for new item">
					<div class="divider"></div>
					<input type="text" name="item-price" id="item-price" placeholder="Price">
				</div>
				<textarea id="item-description" name="item-description" placeholder="Add a description of the item"></textarea>
				<div class="dropzone-previews"></div>
				<div id="location-details">
					<div class="location-image"></div>
					<input type="text" id="the-location" placeholder="Where are you?" />
				</div>
				<div id="bottom-container">
					<div class="left">
						<select id="category" name="category">
							<option selected disabled>Choose Category</option>
							<optgroup label="Clothing -">
								<option value="1">Mens Clothing</option>
								<option value="2">Womens Clothing</option>
								<option value="3">Unisex Clothing</option>
								<option value="4">Hats</option>
								<option value="5">Misc</option>
								<option disabled>-</option>
							</optgroup>
							<optgroup label="Rideshare">
								<option value="6">Local</option>
								<option value="7">Long Distance</option>
								<option disabled>-</option>
							</optgroup>
							<optgroup label="Books">
								<option value="8">Textbooks</option>
								<option value="9">Leisure</option>
								<option disabled>-</option>
							</optgroup>
							<optgroup label="Jobs">
								<option value="10">Seeking - Internships</option>
								<option value="11">Seeking - Part Time</option>
								<option value="12">Hiring - Internsips</option>
								<option value="13">Hiring - Part Time</option>
								<option disabled>-</option>
							</optgroup>
							<option value="14">Lost and Found</option>
							<option value="15">Electronics</option>
							<option value="16">Tutoring and Study Groups</option>
						</select>
						<label id="file-upload-label">Upload Images +</label>
						{{ Form::file('file-upload', ['multiple' => 'multiple','id' => 'file-upload']); }}
					</div>
					<div class="right">
					<input type="submit" id="file-upload-submit" value="Post Item">
					</div>
				</div>
				<input type="hidden" name="_uuid" value="{{ Uuid::generate(); }}">
				{{ Form::token() }}
			</form>
		</div>
		@foreach ($posts as $post)
		<div class="the-post">
			<span class="the-post-head">
				<h1 class="post-title">{{{ $post->title }}}&nbsp;-&nbsp; </h1>
				<h1 class="the-price"> {{{ $post->price }}}</h1>
			</span>
			<span class="post-content">
				<p class="post-descripton"> {{ nl2br($post->content,false) }} </p>
			</span>
			<span class="post-images" id="{{$post->id}}">
				<div class="grid-sizer"></div>
				<div class="gutter-sizer"></div>
		 		@foreach($post->getPostImages() as $image)
		 		<div class="image-container items">
					<img src="{{ $image->path }}" class="the-images" />
				</div>
				@endforeach
			</span>
		</div>
		@endforeach
	</div>
	<div class="column right-column">

	</div>
</section>

@stop
