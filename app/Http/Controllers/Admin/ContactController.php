<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Contact;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ], [
            'mobile_number.required' => 'Mobile Number is Empty!',
            'subject.required' => ' Subject is Empty!',
            'message.required' => 'Message is Empty!',

        ]);
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile_number = $request->mobile_number;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        return response()->json(['success' => 'Your Message Send Successfully']);
    }

    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $contacts = Contact::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('mobile_number', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $contacts = new Contact();
        }
        $contacts = $contacts->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.contacts.list', compact('contacts','search'));

    }

    public function view($id)
    {
        $contact = Contact::findOrFail($id);
        $mail_config = Helpers::get_business_settings('mail_config');
        return view('admin-views.contacts.view', compact('contact', 'mail_config'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);
        $contact->feedback = $request->feedback;
        $contact->seen = 1;
        $contact->update();
        Toastr::success(translate('Feedback_Update_successfully'));
        return redirect()->route('admin.contact.list');
    }

    public function destroy(Request $request)
    {
        $contact = Contact::find($request->id);
        $contact->delete();

        return response()->json();
    }

    public function send_mail(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $data = array('body' => $request['mail_body']);

        $emailServices_smtp = Helpers::get_business_settings('mail_config');
        Log::info('send_mail: mail_config loaded', ['config' => $emailServices_smtp]);
        if ($emailServices_smtp['status'] == 0) {
            $emailServices_smtp = Helpers::get_business_settings('mail_config_sendgrid');
            Log::info('send_mail: fell back to mail_config_sendgrid', ['config' => $emailServices_smtp]);
        }

        if ($emailServices_smtp['status'] == 1) {
            try {
                Mail::send('email-templates.customer-message', $data, function ($message) use ($contact, $request, $emailServices_smtp) {
                    $message->from($emailServices_smtp['email_id'], $emailServices_smtp['name'])
                        ->to($contact['email'], BusinessSetting::where(['type' => 'company_name'])->first()->value)
                        ->subject($contact['subject']);
                });

                Contact::where(['id' => $id])->update([
                    'reply' => json_encode([
                        'subject' => $contact['subject'],
                        'body' => $request['mail_body']
                    ])
                ]);

                Toastr::success(translate('Mail_sent_successfully'));
            } catch (\Throwable $th) {
                Log::error('Contact mail send failed for ID ' . $id . ': ' . $th->getMessage());
                Toastr::error(translate('Mail_Sent_Unsuccessful'));
            }
        } else {
            Toastr::error(translate('Configure_your_mail_setup_first'));
        }

        return back();
    }
}
