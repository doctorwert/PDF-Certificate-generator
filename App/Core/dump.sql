SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Структура таблицы `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(25) NOT NULL,
  `curs` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `number` varchar(32) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Индексы таблицы `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`);

--
-- AUTO_INCREMENT для таблицы `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
