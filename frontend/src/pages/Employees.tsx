// src/pages/EmployeeList.tsx
import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';

type Employee = {
  id: number;
  name: string;
  lastName: string;
  email: string;
  position: string;
  birthDate: string;
};

const Employees = () => {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [error, setError] = useState('');
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState('');

  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (!token) {
      navigate('/login');
      return;
    }

    fetchEmployees(token, page, search);
  }, [navigate, page]);

  const fetchEmployees = async (token: string, page: number, search: string) => {
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        perPage: '5',
        search: search.trim()
      });

      const response = await fetch(`http://localhost:8000/api/employees?${params.toString()}`, {
        headers: {
          Authorization: 'Bearer ' + token,
        },
      });

      if (!response.ok) {
        setError('Failed to fetch employees');
        return;
      }

      const data = await response.json();
      setEmployees(data.employees);
      setTotalPages(Math.ceil(data.total / 5));
    } catch (err) {
      setError('Error connecting to server');
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this employee?')) return;

    try {
      const token = localStorage.getItem('token');

      const response = await fetch(`http://localhost:8000/api/employees/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.ok) {
        toast.success('Employee successfully deleted');
        fetchEmployees(token!, page, search);
      }
    } catch (error) {
      console.error(error);
      toast.error('Unexpected error');
    }
  };

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const token = localStorage.getItem('token');
    setPage(1);
    fetchEmployees(token!, 1, search);
  };

  return (
    <div>
      <h2>Employees</h2>

      <form onSubmit={handleSearchSubmit}>
        <input
          type="text"
          placeholder="Search by name..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <button type="submit">Find</button>
      </form>

      {error && <p style={{ color: 'red' }}>{error}</p>}
      {employees.length === 0 && <p>No employees found.</p>}

      <ul>
        {employees.map((emp) => (
          <li key={emp.id}>
            <strong>{emp.name} {emp.lastName}</strong> — {emp.position} — {emp.email}{' '}
            <Link to={`/employees/edit/${emp.id}`}>Edit</Link>{' '}
            <button onClick={() => handleDelete(emp.id)}>Delete</button>
          </li>
        ))}
      </ul>

      {totalPages > 1 && (
        <div>
          <button disabled={page === 1} onClick={() => setPage(page - 1)}>Previous</button>
          <span> Page {page} of {totalPages} </span>
          <button disabled={page === totalPages} onClick={() => setPage(page + 1)}>Next</button>
        </div>
      )}
    </div>
  );
};

export default Employees;
