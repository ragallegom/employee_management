// src/pages/EditEmployee.tsx
import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import Button from 'react-bootstrap/Button';
import Col from 'react-bootstrap/Col';
import Form from 'react-bootstrap/Form';
import InputGroup from 'react-bootstrap/InputGroup';
import Row from 'react-bootstrap/Row';

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

      const data = await response.json()
      const birthDate = data.birthDate?.date?.slice(0, 10) ?? ''
      
      setForm({
        ...data,
        birthDate
      })
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
      <Form onSubmit={handleSubmit}>
        <Row className='mb-3'>
          <Form.Group as={Col} md="4">
            <Form.Label>First Name</Form.Label>
            <Form.Control name="name" placeholder="First Name" value={form.name} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>
          <Form.Group as={Col} md="4">
            <Form.Label>Last Name</Form.Label>
            <Form.Control name="lastName" placeholder="Last Name" value={form.lastName} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Email</Form.Label>
            <InputGroup hasValidation>
              <InputGroup.Text id="inputGroupPrepend">@</InputGroup.Text>
              <Form.Control name="email" type="email" placeholder="Email" value={form.email} onChange={handleChange} required />
              <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
            </InputGroup>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Position</Form.Label>
            <Form.Control name="position" placeholder="Position" value={form.position} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Birht Date</Form.Label>
            <Form.Control name="birthDate" type="date" value={form.birthDate} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
        </Row>
        <Button type="submit">Update</Button>
      </Form>

      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p style={{ color: 'green' }}>{success}</p>}
    </div>
  )
}

export default EditEmployee
