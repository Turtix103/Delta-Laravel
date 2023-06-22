<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';

class LegacyController extends Controller
{
    public function index()
    {
        return view('legacy.index');
    }

    public function login(Request $request)
    {
        session_start();
    
        if ($request->has('username') && $request->has('password')) {
            $username = $request->input('username');
            $password = $request->input('password');
    
            $user = DB::table('employee')->where('username', $username)->first();
    
            if ($user && password_verify($password, $user->password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user->employee_id;
                $_SESSION['isAdmin'] = $user->admin;
    
                return redirect()->route('legacy.mainMenu');
            } else {
                echo 'Invalid username or password';
            }
        }
    }

    public function mainMenu()
    {
        return view('legacy.mainMenu');
    }

    public function changePassword(Request $request)
    {
        session_start();
        if ($request->isMethod('post')) {
            $password = $request->input('password');
            $password2 = $request->input('password2');

            if ($password !== $password2) {
                return "Hesla se neschodují!";
            } else {
                $userId = $_SESSION['user_id'];
                $hashedPassword = Hash::make($password);

                $affected = DB::table('employee')
                    ->where('employee_id', $userId)
                    ->update(['password' => $hashedPassword]);

                if ($affected) {
                    return "Heslo změněno!";
                } else {
                    return "Error updating password";
                }
            }
        }
    }

    public function addEmployee(Request $request)
    {
        session_start();
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return redirect()->route('legacy.index');
        }

        if ($request->isMethod('post')) {
            $name = $request->input('name');
            $surname = $request->input('surname');
            $job = $request->input('job');
            $room = $request->input('room');
            $wage = $request->input('wage');


            return redirect()->back();
        }

        $rooms = DB::table('room')->get();

        return view('legacy.add_employee', compact('rooms'));
    }


    public function employeeCard(Request $request)
    {
        session_start();
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return redirect()->route('legacy.index');
        }
        
        $id = $request->input('id');

        if (!$id) {
            throwError(400);
        }

        $employee = DB::table('employee')->where('employee_id', $id)->first();
        $keys = DB::table('key')->where('employee', $id)->orderBy('key_id')->get();

        if (!$employee) {
            throwError(404);
        }

        return view('legacy.employee_card', compact('employee', 'keys'));
    }

    public function editEmployee(Request $request)
    {
        session_start();
        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            return redirect()->route('legacy.index');
        }
        
        $id = $request->input('id');

        if (!$id) {
            throwError(400);
        }

        $employee = DB::table('employee')->where('employee_id', $id)->first();
        $rooms = DB::table('room')->orderBy('name')->get();
        $keyRooms = DB::table('room')
            ->leftJoin('key', function ($join) use ($id) {
                $join->on('room.room_id', '=', 'key.room')
                    ->where('key.employee', '=', $id);
            })
            ->select('room.*', 'key.employee')
            ->get();

        if (!$employee) {
            throwError(404);
        }

        return view('legacy.edit_employee', compact('employee', 'rooms', 'keyRooms'));
    }

    public function deleteEmployee(Request $request)
    {
        session_start();

        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            return redirect()->route('legacy.index');
        }
        
        if ($request->isMethod('post')) {
            $employeeId = $request->input('employee_id');

            DB::beginTransaction();

            DB::table('key')->where('employee', $employeeId)->delete();
            DB::table('employee')->where('employee_id', $employeeId)->delete();

            DB::commit();

            return redirect()->route('legacy.employees_list');
        } elseif ($request->isMethod('get')) {
            $employeeId = $request->input('id');

            $employee = DB::table('employee')->where('employee_id', $employeeId)->first();

            if (!$employee) {
                throwError(404);
            }

            // Pass the data to the view
            return view('legacy.delete_employee', compact('employee'));
        }
    }

    public function updateEmployee(Request $request)
    {
        session_start();
        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            return redirect()->route('legacy.index');
        }
        
        if ($request->isMethod('post')) {
            $employeeId = $request->input('employee_id');

            $stmt = $pdo->prepare('SELECT * FROM employee WHERE employee_id = ?');
            $stmt->execute([$employeeId]);
            $currentEmployee = $stmt->fetch();

            $name = $request->input('name') ?: $currentEmployee['name'];
            $surname = $request->input('surname') ?: $currentEmployee['surname'];
            $job = $request->input('job') ?: $currentEmployee['job'];
            $wage = $request->input('wage') ?: $currentEmployee['wage'];
            $room = $request->input('room') ?: $currentEmployee['room'];

            DB::beginTransaction();

            DB::table('employee')
                ->where('employee_id', $employeeId)
                ->update([
                    'name' => $name,
                    'surname' => $surname,
                    'job' => $job,
                    'wage' => $wage,
                    'room' => $room,
                ]);

            $rooms = $request->input('rooms') ?: [];

            $keys = DB::table('key')
                ->where('employee', $employeeId)
                ->get();

            if (empty($rooms)) {
                // The user did not check or uncheck any checkboxes, so delete all keys
                foreach ($keys as $key) {
                    DB::table('key')
                        ->where('employee', $employeeId)
                        ->where('room', $key->room)
                        ->delete();
                }
            } else {
                foreach ($keys as $key) {
                    $roomId = $key->room;
                    if (in_array($roomId, $rooms)) {
                        // The key is already assigned to the employee and the checkbox is checked, do nothing
                        $index = array_search($roomId, $rooms);
                        unset($rooms[$index]);
                    } else {
                        // The key is assigned to the employee but the checkbox is unchecked, delete the key
                        DB::table('key')
                            ->where('employee', $employeeId)
                            ->where('room', $roomId)
                            ->delete();
                    }
                }
                foreach ($rooms as $roomId) {
                    // The key is not assigned to the employee and the checkbox is checked, add the key
                    DB::table('key')->insert([
                        'employee' => $employeeId,
                        'room' => $roomId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('legacy.employee_card', ['id' => $employeeId]);
        }
    }

    public function roomsList(Request $request)
    {
        session_start();

        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: index.php");
            exit;
        }

        if ($request->isMethod('post')) {
            $name = trim($request->input('name'));
            $no = trim($request->input('no'));
            $phone = trim($request->input('phone'));

            $affected = DB::table('room')->insert([
                'name' => $name,
                'no' => $no,
                'phone' => $phone
            ]);

            if ($affected) {
                return redirect()->back();
            } else {
                return "Error inserting room";
            }
        }

        $sort = $request->query('poradi', '');
        $sortQuery = 'SELECT * FROM room';

        switch ($sort) {
            case 'nazev_down':
                $sortQuery .= ' ORDER BY name DESC';
                break;
            case 'cislo_down':
                $sortQuery .= ' ORDER BY no DESC';
                break;
            case 'telefon_down':
                $sortQuery .= ' ORDER BY phone DESC';
                break;
            case 'nazev_up':
                $sortQuery .= ' ORDER BY name ASC';
                break;
            case 'cislo_up':
                $sortQuery .= ' ORDER BY no ASC';
                break;
            case 'telefon_up':
                $sortQuery .= ' ORDER BY phone ASC';
                break;
        }

        $sortStmt = DB::select($sortQuery);

        return view('rooms_list', [
            'sortStmt' => $sortStmt,
            'sort' => $sort
        ]);
    }

    public function roomCard(Request $request)
    {
        session_start();

        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: index.php");
            exit;
        }

        $id = $request->query('id');

        if (!$id) {
            return "Error: Invalid room ID.";
        }

        $stmt = DB::table('room')->where('room_id', $id)->first();

        if (!$stmt) {
            return "Error: Room not found.";
        }

        $room = (array) $stmt;

        return view('room_card', [
            'room' => $room
        ]);
    }

    public function editRoom(Request $request)
    {
        session_start();

        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            header("Location: index.php");
            exit;
        }

        $id = $request->query('id');

        if (!$id) {
            return "Error: Invalid room ID.";
        }

        $stmt = DB::table('room')->where('room_id', $id)->first();

        if (!$stmt) {
            return "Error: Room not found.";
        }

        $room = (array) $stmt;

        return view('edit_room', [
            'room' => $room
        ]);
    }

    public function deleteRoom(Request $request)
    {
        session_start();

        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            header("Location: index.php");
            exit;
        }

        if ($request->isMethod('post')) {
            try {
                $roomId = $request->input('room_id');

                DB::table('room')->where('room_id', $roomId)->delete();

                return redirect()->route('rooms.list');
            } catch (\PDOException $e) {
                return "Error: " . $e->getMessage();
            }
        }

        if ($request->isMethod('get')) {
            $roomId = $request->query('id');

            $room = DB::table('room')->where('room_id', $roomId)->first();

            if (!$room) {
                return "Error: Room not found.";
            }

            return view('delete_room', [
                'room' => $room
            ]);
        }
    }

    public function updateRoom(Request $request)
    {
        session_start();

        if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
            header("Location: index.php");
            exit;
        }

        if ($request->isMethod('post')) {
            $roomId = $request->input('room_id');
            $name = $request->input('name');
            $no = $request->input('no');
            $phone = $request->input('phone');

            // Check for duplicates
            $count = DB::table('room')
                ->where('phone', $phone)
                ->where('room_id', '!=', $roomId)
                ->count();

            if ($count > 0) {
                return redirect()->route('room.card', ['id' => $roomId, 'error' => 'duplicate_phone']);
            }

            // Update the row
            DB::table('room')
                ->where('room_id', $roomId)
                ->update([
                    'name' => $name,
                    'no' => $no,
                    'phone' => $phone
                ]);

            return redirect()->route('room.card', ['id' => $roomId]);
        }
    }

}