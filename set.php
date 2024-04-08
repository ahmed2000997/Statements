<?php
// بيانات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xapi";

// إنشاء اتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

while (true) {
    // استعلام لاسترداد عدد الصفوف في الجدول new_students
    $count_sql = "SELECT COUNT(*) as count FROM new_students";
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $new_students_count = $count_row['count'];

    // استعلام لاسترداد عدد الصفوف في الجدول students
    $count_sql = "SELECT COUNT(*) as count FROM students";
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $students_count = $count_row['count'];

    // إذا كان عدد الصفوف في الجدول students أكبر من عدد الصفوف في الجدول new_students
    if ($students_count > $new_students_count) {
        $diff = $students_count - $new_students_count;

        // استعلام لاسترداد أحدث $diff صفوف من الجدول students
        $latest_students_sql = "SELECT * FROM students ORDER BY id DESC LIMIT $diff";
        $latest_students_result = $conn->query($latest_students_sql);

        if ($latest_students_result->num_rows > 0) {
            while ($row = $latest_students_result->fetch_assoc()) {
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $date_of_birth = $row["date_of_birth"];
                $email = $row["email"];
                $phone_number = $row["phone_number"];
                $address = $row["address"];

                // إدخال بيانات الطالب في جدول new_students
                $insert_sql = "INSERT INTO new_students (first_name, last_name, date_of_birth, email, phone_number, address) 
                                VALUES ('$first_name', '$last_name', '$date_of_birth', '$email', '$phone_number', '$address')";
                if ($conn->query($insert_sql) === TRUE) {
                    // تمت إضافة الطالب بنجاح
                    echo "تمت إضافة الطلاب الجدد بنجاح";
                } else {
                    echo "حدث خطأ أثناء إضافة الطلاب الجدد: " . $conn->error;
                }
            }
        }
    }

    // انتظار لبعض الوقت قبل البحث عن طلاب جدد مرة أخرى
    sleep(10); // انتظر 5 ثواني قبل الاستمرار في الحلقة
}

// إغلاق اتصال قاعدة البيانات
$conn->close();
?>
