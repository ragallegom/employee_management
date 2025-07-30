// src/components/Navbar.tsx
import { useNavigate } from 'react-router-dom'

import Nav from 'react-bootstrap/Nav';
import { Button } from 'react-bootstrap';
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

const Navbar = () => {
  const navigate = useNavigate()
  const [isLoggedIn, setIsLoggedIn] = useState(false)

  useEffect(() => {
    const token = localStorage.getItem('token')
    setIsLoggedIn(!!token)
  }, [])

  const handleLogout = () => {
    localStorage.removeItem('token')
    setIsLoggedIn(false)
    navigate('/login')
  }

  return (
    <Nav variant='pills'>
      <Nav.Item>
        <Nav.Link eventKey="1" href="/dashboard">Dashboard</Nav.Link>
      </Nav.Item>
      <Nav.Item>
        <Nav.Link eventKey="1" href="/employees">Employees</Nav.Link>
      </Nav.Item>
      <Nav.Item>
        <Nav.Link eventKey="1" href="/employees/create">New Employee</Nav.Link>
      </Nav.Item>
      <Nav.Item>
        {isLoggedIn ? (
          <Button onClick={handleLogout}>Logout</Button>
        ) : (
          <Button href="/login" className="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded">
            Login
          </Button>
        )}
      </Nav.Item>
    </Nav>
  )
}

export default Navbar
