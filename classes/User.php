<?php
class User
{
    private $db;
    private $table = 'users';

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (full_name, email, password, age, profile_picture, created_at)
VALUES (:full_name, :email, :password, :age, :profile_picture, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':age' => $data['age'],
            ':profile_picture' => $data['profile_picture']
        ]);
    }

    public function emailExists($email)
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0;
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['full_name']) && $data['full_name'] !== '') {
            $fields[] = 'full_name = :full_name';
            $params[':full_name'] = $data['full_name'];
        }

        if (isset($data['age']) && $data['age'] !== '' && $data['age'] !== null) {
            $fields[] = 'age = :age';
            $params[':age'] = (int)$data['age'];
        }

        if (isset($data['profile_picture']) && $data['profile_picture'] !== '') {
            $fields[] = 'profile_picture = :profile_picture';
            $params[':profile_picture'] = $data['profile_picture'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
