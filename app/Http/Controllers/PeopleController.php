<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PeopleController extends Controller
{
    private function nextRunningCode(string $modelClass, string $prefix, int $pad = 6): string
    {
        $max = $modelClass::query()
            ->whereNotNull('code')
            ->pluck('code')
            ->filter(static fn ($code) => preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $code) === 1)
            ->map(static function ($code) use ($prefix) {
                return (int) substr((string) $code, strlen($prefix));
            })
            ->max();

        $next = ($max ?? 0) + 1;

        return $prefix . str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'customers');
        if (!in_array($tab, ['customers', 'suppliers', 'staff'], true)) {
            $tab = 'customers';
        }

        $q = $request->get('q', '');

        $customers = Customer::when($q, fn($query) =>
            $query->where('full_name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")
        )->orderBy('code')->paginate(20, ['*'], 'cpage')->withQueryString();

        $suppliers = Supplier::when($q, fn($query) =>
            $query->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")
        )->orderBy('code')->paginate(20, ['*'], 'spage')->withQueryString();

        $staff = User::when($q, fn($query) =>
            $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
        )->orderBy('id')->paginate(20, ['*'], 'upage')->withQueryString();

        $nextCustomerCode = $this->nextCode('customer');
        $nextSupplierCode = $this->nextCode('supplier');

        return view('people.index', compact('tab', 'customers', 'suppliers', 'staff', 'q', 'nextCustomerCode', 'nextSupplierCode'));
    }

    /**
     * Get next running code for customer or supplier
     */
    private function nextCode(string $model): string
    {
        $table = $model === 'customer' ? 'customers' : 'suppliers';
        $prefix = $model === 'customer' ? 'C' : 'S';
        $last = \DB::table($table)->whereNotNull('code')->orderByDesc('code')->value('code');
        if (!$last) return $prefix . '0001';
        $num = (int) preg_replace('/\D/', '', $last);
        return $prefix . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
    }

    public function storeCustomer(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:200',
            'id_card' => 'nullable|string|max:20',
            'hn' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'food_allergy' => 'nullable|string',
            'other_allergy' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'is_alert' => 'boolean',
            'alert_note' => 'nullable|string',
            'warning_note' => 'nullable|string',
        ]);

        do {
            $code = $this->nextRunningCode(Customer::class, 'C');
        } while (Customer::where('code', $code)->exists());

        $data['code'] = $code;
        $data['is_alert'] = $request->boolean('is_alert');
        Customer::create($data);

        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'เพิ่มลูกค้าเรียบร้อยแล้ว');
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:200',
            'id_card' => 'nullable|string|max:20',
            'hn' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'food_allergy' => 'nullable|string',
            'other_allergy' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'is_alert' => 'boolean',
            'alert_note' => 'nullable|string',
            'warning_note' => 'nullable|string',
        ]);

        $data['is_alert'] = $request->boolean('is_alert');
        $customer->update($data);

        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'อัพเดตลูกค้าเรียบร้อยแล้ว');
    }

    public function destroyCustomer(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'ลบลูกค้าเรียบร้อยแล้ว');
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
        ]);

        do {
            $code = $this->nextRunningCode(Supplier::class, 'S');
        } while (Supplier::where('code', $code)->exists());

        $data['code'] = $code;
        $data['is_disabled'] = false;
        Supplier::create($data);

        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'เพิ่มผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
            'is_disabled' => 'boolean',
        ]);

        $data['is_disabled'] = $request->boolean('is_disabled');
        $supplier->update($data);

        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'อัพเดตผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function destroySupplier(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'ลบผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
    }

    public function updateStaff(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'อัพเดตพนักงานเรียบร้อยแล้ว');
    }

    public function destroyStaff(User $user)
    {
        $user->delete();

        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'ลบพนักงานเรียบร้อยแล้ว');
    }
}