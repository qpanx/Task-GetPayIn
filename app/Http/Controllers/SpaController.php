<?php

namespace App\Http\Controllers;

class SpaController extends Controller
{
    /**
     * Serve the single page application.
     */
    public function index()
    {
        return view('app');
    }
} 