// src/pages/EditEmployee.tsx
import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { toast } from 'react-toastify'

type Employee = {
  id: number
  name: string
  lastName: string
  email: string
  position: string
  birthDate: string
}

const EditEmployee = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const [form, setForm] = useState<Employee | null>(null)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')

  useEffect(() => {
    const token = localStorage.getItem('token')
    if (!token) {
      navigate('/login')
      return
    }

    const fetchEmployee = async () => {
      const response = await fetch(`http://localhost:8000/api/employees/${id}`, {
        headers: {
          Authorization: 'Bearer ' + token,
        },
      })

      if (!response.ok) {
        setError('Employee not found')
        return
      }

      toast.success("Employee updated successfully!")
      const data = await response.json()
      setForm(data)
    }

    fetchEmployee()
  }, [id, navigate])

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (!form) return
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    const token = localStorage.getItem('token')
    if (!form || !token) return

    const response = await fetch(`http://localhost:8000/api/employees/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Authorization: 'Bearer ' + token,
      },
      body: JSON.stringify(form),
    })

    const data = await response.json()

    if (!response.ok) {
      setError(data.error || data.errors?.join(', ') || 'Update failed')
      return
    }

    setSuccess('Employee updated successfully!')
    setTimeout(() => navigate('/employees'), 1500)
  }

  if (!form) return <p>Loading...</p>

  return (
    <div>
      <h2>Edit Employee</h2>
      <form onSubmit={handleSubmit}>
        <input name="name" value={form.name} onChange={handleChange} required />
        <input name="lastName" value={form.lastName} onChange={handleChange} required />
        <input name="email" value={form.email} onChange={handleChange} required />
        <input name="position" value={form.position} onChange={handleChange} required />
        <input
            type="date"
            name="birthDate"
            value={
                typeof form.birthDate === 'string'
                ? form.birthDate.slice(0, 10)
                : form.birthDate?.date?.slice(0, 10) || ''
            }
            onChange={handleChange}
            required
        />
        <button type="submit">Update</button>
      </form>

      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p style={{ color: 'green' }}>{success}</p>}
    </div>
  )
}

export default EditEmployee
