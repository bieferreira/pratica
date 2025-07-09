import { useState, useEffect } from 'react';

export default function TaskForm({ onSave, selectedTask, clearSelected }) {
  const [task, setTask] = useState({ title: '', description: '', status: 'pendente' });

  useEffect(() => {
    if (selectedTask) setTask(selectedTask);
  }, [selectedTask]);

  const handleChange = e => {
    const { name, value } = e.target;
    setTask(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = e => {
    e.preventDefault();
    onSave(task);
    setTask({ title: '', description: '', status: 'pendente' });
    clearSelected();
  };

  return (
    <form onSubmit={handleSubmit} className="mb-4">
      <div className="mb-3">
        <label className="form-label">Título</label>
        <input name="title" className="form-control" value={task.title} onChange={handleChange} required />
      </div>
      <div className="mb-3">
        <label className="form-label">Descrição</label>
        <textarea name="description" className="form-control" value={task.description} onChange={handleChange} />
      </div>
      <div className="mb-3">
        <label className="form-label">Status</label>
        <select name="status" className="form-select" value={task.status} onChange={handleChange}>
          <option value="pendente">Pendente</option>
          <option value="concluida">Concluída</option>
        </select>
      </div>
      <div className="d-flex justify-content-center">
      <button type="submit" className="btn btn-primary">
        {task.id ? 'Atualizar' : 'Criar'} Tarefa
      </button>
      </div>
    </form>
  );
}
