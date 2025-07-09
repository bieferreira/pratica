export default function TaskList({ tasks, onEdit, onDelete }) {
  return (
    <ul className="list-group">
      {tasks.map(task => (
        <li key={task.id} className="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>{task.title}</strong> <span className="badge bg-secondary ms-2">{task.status}</span>
            <div className="small text-muted">{task.description}</div>
          </div>
          <div>
            <button className="btn btn-sm btn-warning me-2" onClick={() => onEdit(task)}>Editar</button>
            <button className="btn btn-sm btn-danger" onClick={() => onDelete(task.id)}>Excluir</button>
          </div>
        </li>
      ))}
    </ul>
  );
}
