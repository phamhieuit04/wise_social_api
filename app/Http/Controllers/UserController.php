<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\SendMail;
use App\Models\ChatRoom;
use App\Models\DeviceToken;
use App\Models\Follow;
use App\Models\Friend;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

	private $apiResponse;

	public function __construct()
	{
		$this->apiResponse = new ApiResponse();
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$param = $request->all();
		$file = null;
		if ($request->hasFile("image")) {
			$file = $request->file('image');
			$fileName = Auth::user()->id . '.' . date('ymdhis') . "." .
				$file->getClientOriginalExtension();
			if (!is_dir(public_path('post_images'))) {
				mkdir(directory: public_path('post_images'));
			}
			move_uploaded_file(
				$file,
				public_path('post_images') . "\\" . $fileName
			);
		}
		$post = new Post();
		$post->user_id = Auth::user()->id;
		$post->content = $param['content'];
		$post->timeline_orders = Carbon::now();
		$post->view_count = 0;
		$post->images = $fileName;
		$post->save();
		// insert to notifications table
		$friends = Friend::where('user_id', Auth::user()->id)
			->get();
		if (count($friends) > 0) {
			$arrPushToFriend = [];
			foreach ($friends as $friend) {
				$arrPushToFriend[] = [
					'user_id' => $friend->friend_id,
					'actor_id' => Auth::user()->id,
					'content' => '「' . Auth::user()->name . '」さんがあなたの投稿にコメントしました。',
					'is_view' => Notification::UNVIEWED,
					'status' => Notification::STATUS_WAIT
				];
			}
			DB::table('notifications')->insert($arrPushToFriend);
		}
		return $this->apiResponse->success($post);
	}

	/**
	 * Display the specified resource.
	 *
	 * @return string JSON
	 */
	public function show(Request $request)
	{
		$userId = Auth::id();
		if (!$userId) {
			return $this->apiResponse->UnAuthorization();
		}
		$user = User::with('follower', 'follows')
			->select(
				'id',
				'name',
				'email',
				'avatar',
				'overview'
			)->where('id', $userId)->first();
		$user->followers = count($user->follower);
		$user->following = count($user->follows);
		unset($user->follower, $user->follows);
		$folderAvatar = null;
		if (!is_null($user->avatar)) {
			$folderAvatar = explode('@', $user->email);
			$user->avatar = url(
				'avatars/' . $folderAvatar[0] . '/' . $user->avatar
			);
		}
		return $this->apiResponse->success($user);
	}

	/**
	 * Controller method suggest friend
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return bool|string
	 */
	public function suggestFriend(Request $request)
	{
		$userId = Auth::id();
		// List friend id
		$listFriendId = DB::table('friends')
			->where('user_id', $userId)
			->select('friend_id')
			->pluck('friend_id')->toArray();
		$listFriendId[] = $userId;
		// List sugget friend
		$suggests = User::with([
			'experiences' => function ($experienceQuery) {
				return $experienceQuery->select('id', 'user_id', 'title');
			}
		])->whereNotIn('id', $listFriendId)
			->where('status', User::STATUS_ACTIVE)
			->select(
				'id',
				'name',
				'avatar',
				'created_at'
			)->orderBy('created_at', 'ASC')
			->limit(config('constant.limit'))
			->get();
		if (count($suggests) > 0) {
			foreach ($suggests as $user) {
				$folderAvatar = null;
				if (!is_null($user->avatar)) {
					$folderAvatar = explode('@', $user->email);
					$user->avatar = url(
						'avatars/' . $folderAvatar[0] . '/' . $user->avatar
					);
				}
				$txtExperience = '';
				$i = 1;
				foreach ($user->experiences as $experience) {
					if ($i < count($user->experiences)) {
						$txtExperience .= $experience->title . ', ';
					} else {
						$txtExperience .= $experience->title;
					}
					$i++;
				}
				$user->experience = $this->truncateString($txtExperience, 20);
				unset($user->experiences);
			}
		}
		return $this->apiResponse->success($suggests);
	}

	private function truncateString($string, $length, $append = '...')
	{
		if (mb_strlen($string) > $length) {
			return mb_substr($string, 0, $length) . $append;
		}
		return $string;
	}

	public function listFriendRequest(Request $request)
	{
		$userId = Auth::id();
		// List sugget friend
		$requests = User::with([
			'experiences' => function ($experienceQuery) {
				return $experienceQuery->select('id', 'user_id', 'title');
			}
		])->join(
				'friends',
				'users.id',
				'friends.user_id'
			)->where('friends.friend_id', $userId)
			->where('users.status', User::STATUS_ACTIVE)
			->where('friends.approved', Friend::UN_APPROVED)
			->select(
				'friends.id',
				'users.email',
				'users.name',
				'users.avatar',
				'users.created_at'
			)->orderBy('friends.created_at', 'ASC')
			->limit(config('constant.limit'))
			->get();
		if (count($requests) > 0) {
			foreach ($requests as $user) {
				$folderAvatar = null;
				if (!is_null($user->avatar)) {
					$folderAvatar = explode('@', $user->email);
					$user->avatar = url(
						'avatars/' . $folderAvatar[0] . '/' . $user->avatar
					);
				}
				$txtExperience = '';
				$i = 1;
				foreach ($user->experiences as $experience) {
					if ($i < count($user->experiences)) {
						$txtExperience .= $experience->title . ', ';
					} else {
						$txtExperience .= $experience->title;
					}
					$i++;
				}
				$user->experience = $this->truncateString($txtExperience, 15);
				$user->name = $this->truncateString($user->name, 10);
				unset($user->experiences);
			}
		}
		return $this->apiResponse->success($requests);
	}

	/**
	 * Controller method add friend
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return bool|string
	 */
	public function addFriend(Request $request)
	{
		$param = $request->all();
		try {
			DB::beginTransaction();
			$friend = new Friend();
			$friend->user_id = Auth::id();
			$friend->friend_id = $param['friend_id'];
			$friend->approved = Friend::UN_APPROVED;
			$friend->created_at = Carbon::now();
			$friend->save();
			DB::commit();
			// Send mail
			$recUser = User::find($param['friend_id']);
			$sendMail = new SendMail();
			$sendMail->sendMail003($recUser->email, Auth::user());
			// Firebase send notify
			Notification::create([
				'user_id' => $param['friend_id'],
				'actor_id' => Auth::id(),
				'content' => "「'" . Auth::user()->name . "'」さんがあなたに友達リクエストを送信しました。",
				'is_view' => Notification::UNVIEWED,
				'status' => Notification::STATUS_WAIT
			]);
			return $this->apiResponse->success();
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e->getMessage());
			return $this->apiResponse->InternalServerError();
		}
	}

	public function accept(Request $request)
	{
		$param = $request->all();
		if ($param['type'] == 'accept') {
			// Approve
			return DB::table('friends')
				->where('id', $param['id'])
				->update([
					'approved' => Friend::APPROVED
				]);
		} else {
			// Remove
			return DB::table('friends')
				->where('id', $param['id'])
				->delete();
		}
	}

	public function mostFollowed(Request $request)
	{
		$user = User::select([
			'users.id',
			'users.name',
			'users.email',
			'users.avatar'
		])
			->first();
		$folderAvatar = null;
		if (!is_null($user->avatar)) {
			$folderAvatar = explode('@', $user->email);
			$user->avatar = url(
				'avatars/' . $folderAvatar[0] . '/' . $user->avatar
			);
		}
		$txtExperience = '';
		$i = 1;
		foreach ($user->experiences as $experience) {
			if ($i < count($user->experiences)) {
				$txtExperience .= $experience->title . ', ';
			} else {
				$txtExperience .= $experience->title;
			}
			$i++;
		}
		$user->experience = $this->truncateString($txtExperience, 15);
		$user->name = $this->truncateString($user->name, 10);
		unset($user->experiences);
		return $this->apiResponse->success($user);
	}

	public function search(Request $request)
	{
		$param = $request->all();
		$users = User::with([
			'experiences' => function ($experienceQuery) {
				return $experienceQuery->select('id', 'user_id', 'title');
			}
		])->select(
				'id',
				'name',
				'email',
				'avatar'
			)->whereHas('experiences', function ($query) use ($param) {
				return $query->where('title', 'Like', '%' . $param['key-word'] . '%');
			})->orWhere('name', 'Like', '%' . $param['key-word'] . '%')
			->orWhere('email', 'Like', '%' . $param['key-word'] . '%')
			->orderBy('id', 'DESC')->get();
		if (count($users) > 0) {
			foreach ($users as $user) {
				$folderAvatar = null;
				if (!is_null($user->avatar)) {
					$folderAvatar = explode('@', $user->email);
					$user->avatar = url(
						'avatars/' . $folderAvatar[0] . '/' . $user->avatar
					);
				}
				$txtExperience = '';
				$i = 1;
				foreach ($user->experiences as $experience) {
					if ($i < count($user->experiences)) {
						$txtExperience .= $experience->title . ', ';
					} else {
						$txtExperience .= $experience->title;
					}
					$i++;
				}
				$user->experience = $this->truncateString($txtExperience, 100);
				$user->name = $this->truncateString($user->name, length: 100);
				unset($user->experiences);
			}
		}
		return $this->apiResponse->success($users);
	}

	public function setDeviceToken(Request $request)
	{
		$param = $request->all();
		$userId = Auth::user()->id;
		// Check token already exi't
		$checkToken = DeviceToken::where('user_id', $userId)
			->get();
		if (count($checkToken) == 0) {
			$deviceToken = new DeviceToken();
			$deviceToken->user_id = $userId;
			$deviceToken->token = $param['fcmToken'];
			$deviceToken->save();
		} else {
			$deviceToken = DeviceToken::where('user_id', $userId)
				->first();
			$deviceToken->token = $param['fcmToken'];
			$deviceToken->updated_at = Carbon::now();
			$deviceToken->update();
		}
		return $this->apiResponse->success($deviceToken);
	}

	public function listFriend()
	{
		$user = Auth::user();
		$friends = Friend::join('users', 'friends.friend_id', 'users.id')
			->select(['users.id', 'users.name', 'users.email', 'users.avatar', 'users.online_status'])
			->where('friends.user_id', $user->id)
			->where('friends.approved', Friend::APPROVED)
			->orderBy('friends.created_at', 'desc')
			->get();
		$messages = Message::where('user_id', $user->id)
			->orWhere('friend_id', $user->id)
			->orderBy('created_at', 'asc')
			->get();
		foreach ($friends as $friend) {
			$friend->last_message = "";
			foreach ($messages as $message) {
				if ($friend->id == $message->user_id || $friend->id == $message->friend_id) {
					$friend->last_message = $message->message;
					$friend->last_sent = Carbon::create($message->created_at)->diffForHumans();
					continue;
				}
			}
		}
		return $this->apiResponse->success($friends);
	}

	public function listMessage(Request $request)
	{
		$params = $request->all();
		$user = Auth::user();
		$userInRoom = [
			"[" . $user->id . ", " . $params['friend_id'] . "]",
			"[" . $params['friend_id'] . ", " . $user->id . "]",
		];
		$room = ChatRoom::where('user_id', $userInRoom[0])
			->orWhere('user_id', $userInRoom[1])
			->first();
		$roomId = null;
		if (is_null($room)) {
			$createRoom = new ChatRoom();
			$createRoom->user_id = $userInRoom[0];
			$createRoom->save();
			$roomId = $createRoom->id;
		} else {
			$roomId = $room->id;
		}
		$messages = Message::join('users', 'messages.user_id', 'users.id')
			->select([
				'messages.id',
				'users.name',
				'users.avatar',
				'messages.message',
				'messages.user_id',
				'messages.friend_id',
				'messages.is_view'
			])
			->where('messages.room_id', $roomId)
			->orderBy('messages.created_at', 'asc')
			->get()
			->map(function ($item) use ($user, $roomId) {
				if ($item->user_id == $user->id) {
					$item->my_message = "me";
				}
				if ($item->friend_id == $user->id) {
					$item->my_message = 'friend';
				}
				$item->_created_at = Carbon::create($item->created_at)->format('Y-m-d h:i');
				$item->room_id = $roomId;
				$item->type = 'message';
				$item->action = 'join';
				return $item;
			});
		$response = [
			'room_id' => $roomId,
			'messages' => $messages,
			'user_id' => $user->id,
		];
		return $this->apiResponse->success($response);
	}

	public function sendMessage(Request $request)
	{
		$params = $request->all();
		$message = Message::create([
			'user_id' => Auth::user()->id,
			'friend_id' => $params['friend_id'],
			'message' => $params['message_content'],
			'room_id' => $params['room_id'],
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
		$responseData = [
			"id" => $message->id,
			"name" => Auth::user()->name,
			"avatar" => "user-pro-img.png",
			"message" => $params['message_content'],
			"user_id" => Auth::user()->id,
			"friend_id" => $params['friend_id'],
			"is_view" => Message::UNVIEW,
			"created_at" => Carbon::now(),
			"my_message" => "me",
			"_created_at" => Carbon::now()->format('Y-m-d h:i:s'),
			"room_id" => $params['room_id'],
			"type" => "message",
			"action" => "send_message"
		];
		return $this->apiResponse->success($responseData);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(string $id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		//
	}
}
