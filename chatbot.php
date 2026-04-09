<?php
include "config.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$msg = strtolower(trim($data['message'] ?? ''));

/* USER NAME (SESSION se) */
$user_id = $_SESSION['user_id'] ?? 1;
$user_name = $_SESSION['user_name'] ?? "User";

/* CLEAN TEXT */
function clean($text)
{
    return preg_replace("/[^a-z0-9 ]/", "", $text);
}
$msg = clean($msg);

/* SAVE + REPLY */
function reply($conn, $user_id, $msg, $text)
{
    $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, reply) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $msg, $text);
    $stmt->execute();

    echo json_encode(["reply" => $text]);
    exit;
}

/* SMART KEYWORDS */
$intents = [

    "greeting" => ["hi", "hello", "hey", "hlo"],
    "ewaste" => ["ewaste", "waste", "electronic", "kya hota"],
    "pickup" => ["pickup", "collect", "uthao", "ghar se"],
    "price" => ["price", "paise", "kitna", "rate"],
    "login" => ["login", "signin", "account"],
    "register" => ["register", "signup", "account banana"],
    "status" => ["status", "order", "request"],
    "cancel" => ["cancel", "delete"],
    "help" => ["help", "support", "madad"],
    "location" => ["location", "area", "pincode"],
    "time" => ["kitne din", "kab ayega", "kitna time", "delivery", "pickup kab"],

    /* ✅ NEW */
    "thanks" => ["thanks", "thankyou", "thx", "ok", "okay", "oky", "thnx", "ty"],
    "bye" => ["bye", "goodbye", "see you"]

];

/* DETECT INTENT */
function detectIntent($msg, $intents)
{
    foreach ($intents as $intent => $words) {
        foreach ($words as $word) {
            if (strpos($msg, $word) !== false) {
                return $intent;
            }
        }
    }
    return "unknown";
}

$intent = detectIntent($msg, $intents);

/* RESPONSES */

switch ($intent) {

    case "greeting":
        reply($conn, $user_id, $msg, "Hello $user_name 👋\nWelcome to E-Waste Portal!\nMain aapki help ke liye yaha hu 😊");

    case "ewaste":
        reply($conn, $user_id, $msg, "E-waste matlab electronic waste 📱💻 jaise mobile, laptop, charger jo use nahi ho raha.");

    case "pickup":
        reply($conn, $user_id, $msg, "Pickup ke liye dashboard me 'Request Pickup' par click karo 📦 aur details fill karo.");

    case "price":
        reply($conn, $user_id, $msg, "Price item ke type aur condition par depend karta hai 💰");

    case "login":
        reply($conn, $user_id, $msg, "Login karne ke liye apna email aur password use karo 🔐");

    case "register":
        reply($conn, $user_id, $msg, "Register ke liye Sign Up button use karo 📝");

    case "status":
        reply($conn, $user_id, $msg, "Aap apna pickup status 'My Requests' me dekh sakte ho 📊");

    case "cancel":
        reply($conn, $user_id, $msg, "Request cancel karne ke liye dashboard me cancel button use karo ❌");

    case "help":
        reply($conn, $user_id, $msg, "Aap mujhe directly puch sakte ho 😊 ya Contact page use karo 📞");

    case "location":
        reply($conn, $user_id, $msg, "Apna pincode daal kar service availability check karo 📍");

    case "time":
        reply($conn, $user_id, $msg, "Aapka pickup usually 2-3 din ke andar schedule ho jata hai 🚚\nExact timing aapko notification me mil jayegi.");

    /* ✅ NEW CASES */
    case "thanks":
        reply(
            $conn,
            $user_id,
            $msg,
            "Anytime $user_name 😊💚\nAgar aur help chahiye ho to bina hesitate pooch lena!"
        );

    case "bye":
        reply(
            $conn,
            $user_id,
            $msg,
            "Bye $user_name 👋\nHave a great day! 🌟\nKabhi bhi help chahiye ho to wapas aa jana 😊"
        );

    /* DEFAULT */
    default:
        reply(
            $conn,
            $user_id,
            $msg,
            "Oops 😅 Sorry $user_name!\nMujhe aapka question samajh nahi aaya.\n\n👉 Aap ye try kar sakte ho:\n• Pickup kaise kare\n• Price kitna milega\n• Status check kare\n\nMain help ke liye hamesha yaha hu 💚"
        );
}
?>