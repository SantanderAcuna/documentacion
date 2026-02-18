export interface User {
  id: number
  name: string
  email: string
  role: string
  createdAt: string
  updatedAt: string
}

export interface Content {
  id: number
  title: string
  slug: string
  body: string
  status: 'draft' | 'published' | 'archived'
  authorId: number
  createdAt: string
  updatedAt: string
}

export interface Category {
  id: number
  name: string
  slug: string
  description?: string
}

export interface Tag {
  id: number
  name: string
  slug: string
}
