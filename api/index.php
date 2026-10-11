<?php

/**
 * Vercel serverless function entry point. Just defers to Laravel's normal
 * public/index.php front controller — __DIR__ inside that file still
 * resolves relative to public/ itself (PHP resolves __DIR__ by the file's
 * own location, not the includer's), so no path rewriting is needed here.
 */
require __DIR__.'/../public/index.php';
