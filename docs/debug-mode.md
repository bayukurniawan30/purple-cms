# Debug Mode {docsify-ignore-all}

To change debug mode, open file <code>app.php</code> in folder <code>config</code>, find the line with <code>'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN)</code>

**false** = Production mode. No error messages, errors, or warnings shown. 

**true** = Errors and warnings shown.