<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Payment;
use App\Models\Setting;

class PaymentManagementService
{
    public function getPaymentStatistics()
    {
        // جلب نسبة العمولة أو القيمة الافتراضية 15%
        $commissionRate = Setting::first()->commission ?? 15.00;

        // جميع المدفوعات التي من النوع 'payment'
        $totalStudentPayments = Payment::where('type', 'payment')->sum('amount');

        //عدد عمليات السحب
        $totalWithdrawalsCount = Payment::where('type', 'withdrawal')->count();

        // ربح المنصة = المدفوعات * نسبة العمولة
        $platformEarnings = ($totalStudentPayments * $commissionRate) / 100;

        // أرباح المدرسين = المدفوعات - ربح المنصة
        $instructorEarnings = $totalStudentPayments - $platformEarnings;

        $data = [
            'platform_earnings' => round($platformEarnings, 2),
            'instructor_earnings' => round($instructorEarnings, 2),
            'total_withdrawals' => round($totalWithdrawalsCount, 2),
            'student_payments' => round($totalStudentPayments, 2),
        ];

        return ApiResponse::sendResponse(200, 'Payment statistics retrieved successfully', $data);
    }

    public function getAllPayments()
    {
        $payments = Payment::with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $payments->getCollection()->transform(function ($payment) {
            $payload = is_array($payment->response_payload) ? $payment->response_payload : [];
            return [
                'id' => $payment->id,
                'date' => $payment->created_at->format('Y-m-d'),
                'user_name' => $payment->user?->name ?? null,
                'type' => $payment->type,
                'amount' => (float) $payment->amount,
                'status' => $payment->status,
                'payment_method' => $payload['payment_method'] ?? null,
            ];
        });

        return ApiResponse::sendResponse(200, 'All payments retrieved successfully', $payments);
    }

    public function updateWithdrawalStatus(int $id, string $status)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return ApiResponse::sendResponse(404, 'Payment not found');
        }

        if ($payment->type !== 'withdrawal') {
            return ApiResponse::sendResponse(400, 'Only withdrawals can be updated');
        }

        if ($payment->status !== 'pending') {
            return ApiResponse::sendResponse(400, 'Only pending withdrawals can be updated');
        }

        $payment->status = $status;
        $payment->save();

        $message = $status === 'succeeded' ? 'Withdrawal approved successfully' : 'Withdrawal rejected successfully';

        return ApiResponse::sendResponse(200, $message, [
            'id' => $payment->id,
            'status' => $payment->status,
        ]);
    }


    public function getPaymentDetailsById(int $id)
    {
        $payment = Payment::with(['user', 'order.orderItems.course'])->find($id);

        if (!$payment) {
            return ApiResponse::sendResponse(404, 'Payment not found');
        }
        // استخرج الداتا من البيلود
        $payload = is_array($payment->response_payload) ? $payment->response_payload : [];

        $method = $payload['payment_method'] ?? null;

        // لو النوع withdrawal
        if ($payment->type === 'withdrawal') {
            $data = [
                'instructor' => $payment->user?->name,
                'request_date' => $payment->created_at->format('Y-m-d'),
                'amount'       => (float) $payment->amount,
                'method'       => $method,
                'status'       => $payment->status,
            ];
            // إذا الميثود بنك → رجع معلومات إضافية
            if ($method === 'bank') {
                $data['account_number'] = $payload['account_number'] ?? null;
                $data['bank_name']      = $payload['bank_name'] ?? null;
            }
        }

        // لو النوع payment
        elseif ($payment->type === 'payment') {

            // جمع كل الكورسات من كل الأوردرات المرتبطة بهذا الدفع
            $courses = collect($payment->orders)
                ->flatMap(fn($order) => collect($order->items)->map(fn($item) => $item->course?->title))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $data = [
                'student'      => $payment->user?->name,
                'payment_date' => $payment->created_at->format('Y-m-d'),
                'course'       => $courses,
                'method'       => $method,
                'amount'       => (float) $payment->amount,
                'status'       => $payment->status,
            ];
        } else {
            return ApiResponse::sendResponse(400, 'Unsupported payment type');
        }

        return ApiResponse::sendResponse(200, 'Payment details retrieved successfully', $data);
    }
}
