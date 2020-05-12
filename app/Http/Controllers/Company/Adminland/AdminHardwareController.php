<?php

namespace App\Http\Controllers\Company\Adminland;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\InstanceHelper;
use App\Models\Company\Hardware;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Company\Adminland\Hardware\LendHardware;
use App\Services\Company\Adminland\Hardware\CreateHardware;
use App\Services\Company\Adminland\Question\UpdateQuestion;
use App\Services\Company\Adminland\Question\DestroyQuestion;
use App\Http\ViewHelpers\Company\Adminland\AdminHardwareViewHelper;

class AdminHardwareController extends Controller
{
    /**
     * Show the list of hardware.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $company = InstanceHelper::getLoggedCompany();

        $hardware = $company->hardware()->with('employee')->orderBy('created_at', 'desc')->get();
        $hardwareInformation = AdminHardwareViewHelper::hardware($hardware);

        return Inertia::render('Adminland/Hardware/Index', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'hardware' => $hardwareInformation,
            'state' => 'all',
        ]);
    }

    /**
     * Show the Create hardware view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $company = InstanceHelper::getLoggedCompany();
        $employees = AdminHardwareViewHelper::employeesList($company);

        return Inertia::render('Adminland/Hardware/Create', [
            'employees' => $employees,
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
        ]);
    }

    /**
     * Create the question.
     *
     * @param Request $request
     * @param int $companyId
     * @return Response
     */
    public function store(Request $request, int $companyId)
    {
        $company = InstanceHelper::getLoggedCompany();
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $data = [
            'company_id' => $company->id,
            'author_id' => $loggedEmployee->id,
            'name' => $request->input('name'),
            'serial_number' => $request->input('serial'),
        ];

        $hardware = (new CreateHardware)->execute($data);

        if ($request->input('lend_hardware')) {
            (new LendHardware)->execute([
                'company_id' => $company->id,
                'author_id' => $loggedEmployee->id,
                'employee_id' => $request->input('employee_id'),
                'hardware_id' => $hardware->id,
            ]);
        }

        return response()->json([
            'data' => $company->id,
        ], 201);
    }

    /**
     * Create the question.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $hardwareId
     * @return Response
     */
    public function show(Request $request, int $companyId, int $hardwareId)
    {
        $company = InstanceHelper::getLoggedCompany();

        try {
            $hardware = Hardware::where('company_id', $company->id)
                ->with('employee')
                ->findOrFail($hardwareId);
        } catch (ModelNotFoundException $e) {
            return redirect('home');
        }

        $information = [
            'id' => $hardware->id,
            'name' => $hardware->name,
            'serial_number' => $hardware->serial_number,
            'employee' => $hardware->employee ? [
                'id' => $hardware->employee->id,
                'name' => $hardware->employee->name,
                'avatar' => $hardware->employee->avatar,
            ] : null,
        ];

        $history = AdminHardwareViewHelper::history($hardware);

        return Inertia::render('Adminland/Hardware/Show', [
            'hardware' => $information,
            'history' => $history,
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
        ]);
    }

    /**
     * Update the question.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $questionId
     * @return Response
     */
    public function update(Request $request, int $companyId, int $questionId)
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $request = [
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'question_id' => $questionId,
            'title' => $request->input('title'),
            'active' => $request->input('active'),
        ];

        $question = (new UpdateQuestion)->execute($request);

        return response()->json([
            'data' => $question->toObject(),
        ], 200);
    }

    /**
     * Delete the question.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $questionId
     * @return Response
     */
    public function destroy(Request $request, int $companyId, int $questionId)
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $request = [
            'company_id' => $companyId,
            'question_id' => $questionId,
            'author_id' => $loggedEmployee->id,
        ];

        (new DestroyQuestion)->execute($request);

        return response()->json([
            'data' => true,
        ], 200);
    }

    /**
     * Display the list of available hardware.
     *
     * @param Request $request
     * @param int $companyId
     *
     * @return Response
     */
    public function available(Request $request, int $companyId)
    {
        $company = InstanceHelper::getLoggedCompany();
        $hardware = $company->hardware()->with('employee')->orderBy('created_at', 'desc')->get();
        $hardwareInformation = AdminHardwareViewHelper::availableHardware($hardware);

        return Inertia::render('Adminland/Hardware/Index', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'hardware' => $hardwareInformation,
            'state' => 'available',
        ]);
    }

    /**
     * Display the list of lent hardware.
     *
     * @param Request $request
     * @param int $companyId
     *
     * @return Response
     */
    public function lent(Request $request, int $companyId)
    {
        $company = InstanceHelper::getLoggedCompany();
        $hardware = $company->hardware()->with('employee')->orderBy('created_at', 'desc')->get();
        $hardwareInformation = AdminHardwareViewHelper::lentHardware($hardware);

        return Inertia::render('Adminland/Hardware/Index', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'hardware' => $hardwareInformation,
            'state' => 'lent',
        ]);
    }
}
