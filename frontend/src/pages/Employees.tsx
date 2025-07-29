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

    const fetchEmployees = async () => {
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

    fetchEmployees()
  }, [navigate])

  return (
    <div>
      <h2>Employees</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {employees.length === 0 && <p>No employees found.</p>}
      <ul>
        {employees.map((emp) => (
          <li key={emp.id}>
            <strong>{emp.name} {emp.lastName}</strong> — {emp.position} — {emp.email} <Link to={`/employees/edit/${emp.id}`}>Edit</Link>
          </li>
        ))}
      </ul>
    </div>
  )
}

export default Employees
