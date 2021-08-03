<?php
    /**
     * Урезает оригинальный текст, если его длина меньше заданного числа символов
     *
     * @param string $content Оригинальный текст
     * @param int $lengthLimit Лимит на число символов
     *
     * @return string Возвращается либо урезанный текст с ссылкой, либо оригинальный
     */
    function trimContent(string $content, int $lengthLimit = 300): string
    {
        $moreLink = '<a class="post-text__more-link" href="#">Читать далее</a>';
        $contentWords = explode(' ', $content);
        $contentLength = 0;
        $trimmedWords = [];

        foreach ($contentWords as $word) {
            $contentLength += mb_strlen($word) + 1;

            if ($contentLength <= $lengthLimit) {
                $trimmedWords[] = $word;
            } else {
                break;
            }
        }

        $trimmedContent = implode(' ', $trimmedWords);

        return $contentLength > $lengthLimit ? "<p>{$trimmedContent}...</p>{$moreLink}" : "<p>{$content}</p>";
    }

    /**
     * Отображает дату в относительный формате
     *
     * @param string $date Оригинальная дата размещения поста
     *
     * @return string Возвращается относительный формат даты, исходя из оригинальной
     */
    function getRelativeData(string $date): string
    {
        $dateCurrent = date_create();
        $dateCreate = date_create($date);
        $dateDiff = date_diff($dateCurrent, $dateCreate);

        $days = intval($dateDiff->format("%a"));
        $hours = intval($dateDiff->format("%H"));
        $minutes = intval($dateDiff->format("%i"));

        if ($hours > 0 && $hours < 24) {
            $hoursDeclination = get_noun_plural_form($hours, "час", "часа", "часов");

            return "{$hours} {$hoursDeclination} назад";
        }

        if ($days > 0 && $days < 7) {
            $daysDeclination = get_noun_plural_form($days, "день", "дня", "дней");

            return "{$days} {$daysDeclination} назад";
        }

        if ($days >= 7 && $days < 35) {
            $weeks = floor($days / 7);
            $weeksDeclination = get_noun_plural_form($weeks, "неделя", "недели", "недель");

            return "{$weeks} {$weeksDeclination} назад";
        }

        if ($days >= 35) {
            $months = floor($days / 30);
            $monthsDeclination = get_noun_plural_form($months, "месяц", "месяца", "месяцев");

            return "{$months} {$monthsDeclination} назад";
        }

        $minutesDeclination = get_noun_plural_form($minutes, "минута", "минуты", "минут");

        return "{$minutes} {$minutesDeclination} назад";
    }
