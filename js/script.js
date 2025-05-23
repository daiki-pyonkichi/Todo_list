document.addEventListener('DOMContentLoaded', () => {
    const todoForm = document.getElementById('todoForm');
    const todoInput = document.getElementById('todoInput');
    const deadlineInput = document.getElementById('deadlineInput');
    const detailInput = document.getElementById('detailInput');
    const todoList = document.getElementById('todoList');
    const clock = document.getElementById('clock');
    const dateDisplay = document.getElementById('date');
    const taskError = document.getElementById('taskError');
    const deadlineError = document.getElementById('deadlineError');

    // 日付と時計の更新
    function updateDateTime() {
        const now = new Date();
        
        // 時計の更新
        const timeString = now.toLocaleTimeString('ja-JP', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        clock.textContent = timeString;

        // 日付の更新
        const dateString = now.toLocaleDateString('ja-JP', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            weekday: 'long'
        });
        dateDisplay.textContent = dateString;
    }

    // 1秒ごとに日付と時計を更新
    setInterval(updateDateTime, 1000);
    updateDateTime(); // 初期表示

    // フォームのバリデーション
    function validateForm() {
        let isValid = true;
        taskError.textContent = '';
        deadlineError.textContent = '';

        if (!todoInput.value.trim()) {
            taskError.textContent = 'タスク名を入力してください';
            isValid = false;
        }

        if (!deadlineInput.value) {
            deadlineError.textContent = '締め切りを設定してください';
            isValid = false;
        }

        return isValid;
    }

    // タスクの追加
    todoForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        const task = todoInput.value;
        const deadline = deadlineInput.value;
        const detail = detailInput.value;

        try {
            const response = await fetch('api/add_todo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ task, deadline, detail })
            });

            const data = await response.json();

            if (response.ok) {
                todoInput.value = '';
                deadlineInput.value = '';
                detailInput.value = '';
                loadTodos();
            } else {
                if (data.error) {
                    alert(data.error);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('エラーが発生しました。もう一度お試しください。');
        }
    });

    // タスクの読み込み
    async function loadTodos() {
        try {
            const response = await fetch('api/get_todos.php');
            const data = await response.json();
            
            if (data.success && Array.isArray(data.todos)) {
                todoList.innerHTML = '';
                data.todos.forEach(todo => {
                    const todoElement = createTodoElement(todo);
                    todoList.appendChild(todoElement);
                });
            } else {
                throw new Error(data.error || 'タスクの取得に失敗しました。');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('タスクの読み込み中にエラーが発生しました。');
        }
    }

    // タスク要素の作成
    function createTodoElement(todo) {
        const div = document.createElement('div');
        div.className = 'todo-item';
        
        const content = document.createElement('div');
        content.className = 'todo-content';
        
        const mainContent = document.createElement('div');
        mainContent.className = 'todo-main';
        mainContent.textContent = todo.task;

        const detailContent = document.createElement('div');
        detailContent.className = 'todo-detail';
        detailContent.textContent = todo.detail || '詳細はありません';

        const deadline = document.createElement('div');
        deadline.className = 'deadline';
        
        // 締め切りまでの日数を計算
        const deadlineDate = new Date(todo.deadline);
        const now = new Date();
        
        // 日付のみを比較するために時刻をリセット
        deadlineDate.setHours(0, 0, 0, 0);
        now.setHours(0, 0, 0, 0);
        
        // 日数の差を計算
        const diffTime = deadlineDate.getTime() - now.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        // 締め切りの状態に応じてスタイルを設定
        if (diffDays < 0) {
            deadline.className += ' expired';
            deadline.textContent = '期限切れ';
        } else if (diffDays === 0) {
            deadline.className += ' today';
            deadline.textContent = '本日締切';
        } else {
            deadline.className += ' gradient';
            deadline.textContent = `残り${diffDays}日`;
            
            // 30日を最大として色のグラデーションを計算
            const maxDays = 30;
            const progress = Math.min(diffDays, maxDays) / maxDays;
            
            // HSLカラーを使用してグラデーションを生成
            // より鮮やかな色合いに調整
            // 120: 緑、50: 黄緑、30: オレンジ、0: 赤
            let hue;
            if (progress >= 0.8) {
                // 24-30日: 緑 (120)
                hue = 120;
            } else if (progress >= 0.5) {
                // 15-23日: 緑～黄緑 (120-70)
                hue = 70 + (progress - 0.5) * 100;
            } else if (progress >= 0.2) {
                // 6-14日: 黄緑～オレンジ (70-30)
                hue = 30 + (progress - 0.2) * 133;
            } else {
                // 1-5日: オレンジ～赤 (30-0)
                hue = progress * 150;
            }
            
            deadline.style.backgroundColor = `hsl(${hue}, 85%, 40%)`;
        }

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete-btn';
        deleteBtn.textContent = '削除';
        deleteBtn.onclick = async (e) => {
            e.stopPropagation();
            try {
                const response = await fetch('api/delete_todo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: todo.id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    div.remove();
                } else {
                    console.error('Delete error:', data.error);
                    alert('削除に失敗しました：' + (data.error || '不明なエラー'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('削除中にエラーが発生しました。');
            }
        };

        content.appendChild(mainContent);
        content.appendChild(deadline);
        content.appendChild(deleteBtn);
        
        div.appendChild(content);
        div.appendChild(detailContent);

        // クリックで詳細の表示/非表示を切り替え
        div.addEventListener('click', () => {
            detailContent.classList.toggle('visible');
        });

        return div;
    }

    // 初期読み込み
    loadTodos();
});

function getDeadlineClass(deadline) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const deadlineDate = new Date(deadline);
    deadlineDate.setHours(0, 0, 0, 0);
    
    const diffTime = deadlineDate.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'expired';
    if (diffDays === 0) return 'today';
    if (diffDays <= 3) return 'days-3';
    if (diffDays <= 7) return 'days-7';
    if (diffDays <= 14) return 'days-14';
    if (diffDays <= 30) return 'days-30';
    return 'safe';
}

function getDeadlineText(deadline) {
    const deadlineClass = getDeadlineClass(deadline);
    const deadlineDate = new Date(deadline);
    const formattedDate = deadlineDate.toLocaleDateString('ja-JP', {
        month: 'short',
        day: 'numeric'
    });
    
    switch (deadlineClass) {
        case 'expired':
            return `期限切れ (${formattedDate})`;
        case 'today':
            return '今日まで';
        case 'days-3':
            return `残り3日以内 (${formattedDate})`;
        case 'days-7':
            return `残り7日以内 (${formattedDate})`;
        case 'days-14':
            return `残り14日以内 (${formattedDate})`;
        case 'days-30':
            return `残り30日以内 (${formattedDate})`;
        default:
            return `余裕あり (${formattedDate})`;
    }
} 