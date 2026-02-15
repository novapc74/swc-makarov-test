<h1>Новая задача.</h1>
<p><strong>Заголовок:</strong> {{ $task->title }}</p>
<p><strong>Статус:</strong> {{ $task->status }}</p>
@if($task->due_date)
    <p><strong>Срок выполнения:</strong> {{ $task->due_date }}</p>
@endif
<hr>
<p>Удачи в выполнении!</p>
