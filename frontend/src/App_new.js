import { useEffect, useState } from 'react';
import { getTasks, createTask, updateTask, deleteTask } from './api';
import TaskForm from './components/TaskForm';
import TaskList from './components/TaskList';

function App() {
  const [tasks, setTasks] = useState([]);
  const [selectedTask, setSelectedTask] = useState(null);

  const loadTasks = async () => {
    const res = await getTasks();
    setTasks(res.data);
  };

  useEffect(() => {
    loadTasks();
  }, []);

  const handleSave = async task => {
    if (task.id) {
      await updateTask(task.id, task);
    } else {
      await createTask(task);
    }
    loadTasks();
  };

  const handleDelete = async id => {
    await deleteTask(id);
    loadTasks();
  };

  return (
    <div className="container py-4">
      <h1 className="mb-4">Gerenciador de Tarefas</h1>
      <TaskForm onSave={handleSave} selectedTask={selectedTask} clearSelected={() => setSelectedTask(null)} />
      <TaskList tasks={tasks} onEdit={setSelectedTask} onDelete={handleDelete} />
    </div>
  );
}

export default App;
