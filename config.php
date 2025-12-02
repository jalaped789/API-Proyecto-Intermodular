<?php
const DB_DSN  = 'mysql:host=db;dbname=api-proyecto-intermodular;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = 'root';

const JWT_SECRET_KEY = 'N3VzdE1lTDAzQ0xYc1ZqWUtvTnU0cGdOclpIZHhYd2FmUVp5TEt0M1R4eEh2Y29kZg==';
const JWT_ALGORITHM  = 'HS256';
const JWT_EXPIRATION_TIME = 3600; // 1 hora para que el token expire
?>