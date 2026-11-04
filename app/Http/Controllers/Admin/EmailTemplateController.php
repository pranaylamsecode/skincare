<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return Inertia::render('Admin/EmailTemplates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return Inertia::render('Admin/EmailTemplates/Form', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'subject' => 'required|string|max:200',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully.');
    }

    public function sendTest(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'test_email' => 'required|email',
        ]);

        $dummyVars = [
            'name' => 'John Doe (Test)',
            'booking_number' => 'FMZ-TEST-001',
            'service' => 'Nu Skin LumiSpa Glow Facial',
            'date' => date('d M Y'),
            'time' => 'Morning (10:00 AM - 11:30 AM)',
            'specialist' => 'Dhruvi Jyani',
            'phone' => '+91 98765 43210',
            'pincode' => '380007',
            'skin_concern' => 'Dull and tired skin',
            'total_amount' => '999.00',
            'payment_id' => 'pay_test_123456',
        ];

        $sent = EmailNotificationService::send($template->code, $request->test_email, $dummyVars);

        if ($sent) {
            return back()->with('success', "Test email sent to {$request->test_email}.");
        } else {
            return back()->with('error', 'Failed to send test email. Please check your mail driver settings.');
        }
    }
}
