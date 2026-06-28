@extends('errors.minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Forbidden'))
@section('description', __($exception->getMessage() ?: 'Sorry, you are not authorized to access this page.'))
