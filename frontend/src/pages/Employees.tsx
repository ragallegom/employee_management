// src/pages/EmployeeList.tsx
import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { useNavigate } from 'react-router-dom'

type Employee = {
  id: number
  name: string
  lastName: string
  email: string
  position: string
  birthDate: string
}

const Employees = () => {
  const [employees, setEmployees] = useState<Employee[]>([])
  const [error, setError] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    const token = localStorage.getItem('token')
    if (!token) {
      navigate('/login')
      return
    }

    fetchEmployees()
  }, [navigate])

  const fetchEmployees = async () => {
      const token = localStorage.getItem("token");

      try {
        const response = await fetch('http://localhost:8000/api/employees', {
          headers: {
            Authorization: 'Bearer ' + token,
          },
        })

        if (!response.ok) {
          setError('Failed to fetch employees')
          return
        }

        const data = await response.json()
        setEmployees(data)
      } catch (err) {
        setError('Error connecting to server')
      }
    }

    const handleDelete = async (id: number) => {
      if (!confirm("¿Estás seguro de que deseas eliminar este empleado?")) return;

      try {
        const token = localStorage.getItem("token");

        const response = await fetch(`http://localhost:8000/api/employees/${id}`, {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        if (response.ok) {
          fetchEmployees(); 
        }
      } catch (error) {
        console.error(error);
      }
    };

  return (
    <div>
      <h2>Employees</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {employees.length === 0 && <p>No employees found.</p>}
      <ul>
        {employees.map((emp) => (
          <li key={emp.id}>
            <strong>{emp.name} {emp.lastName}</strong> — {emp.position} — {emp.email} 
            <Link to={`/employees/edit/${emp.id}`}>Edit</Link>
            <button onClick={() => handleDelete(emp.id)}>Eliminar</button>
          </li>
        ))}
      </ul>
    </div>
  )
}


export default Employees
