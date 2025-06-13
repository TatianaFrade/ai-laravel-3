<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrderFieldsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $data = $view->getData();
        $mode = $data['mode'] ?? 'edit'; // modes: create, edit, show
        $order = $data['order'] ?? null;

        $readonly = $mode === 'show';
        $isCreate = $mode === 'create';
        $isEdit = $mode === 'edit';
        
        // For employees, all fields except status should be read-only in edit mode
        $isEmployee = Auth::check() && Auth::user()->type === 'employee';
        $readonly = $readonly || ($isEdit && $isEmployee);
        $needsHiddenFields = $isEdit && $isEmployee;

        $dateValue = old('date', $order->date ?? now()->format('Y-m-d'));
        $cancelReason = old('cancel_reason', $order->cancel_reason ?? '');
        $cancelReasonOther = old('cancel_reason_other', $order->cancel_reason_other ?? '');

        $view->with(compact(
            'mode', 
            'readonly', 
            'isCreate', 
            'isEdit', 
            'isEmployee', 
            'needsHiddenFields', 
            'dateValue', 
            'cancelReason', 
            'cancelReasonOther'
        ));
    }
}
