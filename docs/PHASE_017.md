# Phase 017 - Enterprise Routine & Academic Scheduling System

## Overview

This phase implements the complete Enterprise Routine & Academic Scheduling System for the Education ERP. This module handles all academic scheduling including class routines, teacher routines, student routines, exam routines, room management, and academic calendar.

---

## Routine Architecture

```
Academic Session
    ↓
Academic Calendar
    ↓
Time Slots (08:00-08:50, 09:00-09:50)
    ↓
Periods
    ↓
Rooms (Classrooms, Labs)
    ↓
Routine Entries
    ↓
Conflict Detection
    ↓
Publish
```

---

## Completed Tasks

### Models (6 models)

| Model | Description |
|-------|-------------|
| TimeSlot | Time slots for scheduling |
| Period | Period management |
| Room | Room/venue management |
| Routine | Main routine entries |
| AcademicCalendar | Academic calendar events |
| Holiday | Holiday management |

### Controller
- `RoutineController.php` - Complete CRUD and operations

### Service
- `RoutineService.php` - All business logic

### API Routes
- Complete REST API (20+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Routine Types

| Type | Description |
|------|-------------|
| class | Class Routine |
| teacher | Teacher Routine |
| student | Student Routine |
| exam | Exam Routine |
| practical | Practical Routine |
| laboratory | Laboratory Routine |
| special | Special Routine |

---

## Days of Week

| Value | Day |
|-------|-----|
| 0 | Saturday |
| 1 | Sunday |
| 2 | Monday |
| 3 | Tuesday |
| 4 | Wednesday |
| 5 | Thursday |
| 6 | Friday |

---

## Room Types

| Type | Description |
|------|-------------|
| classroom | Regular Classroom |
| laboratory | Laboratory |
| computer_lab | Computer Lab |
| library | Library |
| seminar_hall | Seminar Hall |
| conference_room | Conference Room |

---

## Calendar Event Types

| Type | Description |
|------|-------------|
| class_start | Class Start |
| semester_start | Semester Start |
| semester_end | Semester End |
| exam | Exam |
| holiday | Holiday |
| admission | Admission |
| registration | Registration |
| result | Result |
| event | Event |

---

## Holiday Types

| Type | Description |
|------|-------------|
| national | National Holiday |
| weekly | Weekly Holiday |
| religious | Religious Holiday |
| special | Special Holiday |
| emergency | Emergency Holiday |

---

## API Endpoints

### Routine CRUD
- `GET /api/v1/routines` - List routines
- `POST /api/v1/routines` - Create routine
- `GET /api/v1/routines/{uuid}` - View routine
- `PUT /api/v1/routines/{uuid}` - Update routine
- `DELETE /api/v1/routines/{uuid}` - Delete routine

### Bulk Operations
- `POST /api/v1/routines/bulk` - Bulk create
- `POST /api/v1/routines/publish` - Publish routines

### Generator
- `POST /api/v1/routines/generate` - Auto-generate

### Conflicts
- `POST /api/v1/routines/conflicts` - Check conflicts

### Teacher/Student/Class
- `GET /api/v1/routines/teacher/{id}` - Teacher routine
- `GET /api/v1/routines/student/{id}` - Student routine
- `GET /api/v1/routines/class` - Class routine

### Time Slots
- `GET /api/v1/routines/time-slots` - List time slots
- `POST /api/v1/routines/time-slots` - Create time slot

### Rooms
- `GET /api/v1/routines/rooms` - List rooms
- `POST /api/v1/routines/rooms` - Create room

### Calendar
- `GET /api/v1/routines/calendar` - Get calendar
- `POST /api/v1/routines/calendar` - Create event

### Holidays
- `GET /api/v1/routines/holidays` - List holidays
- `POST /api/v1/routines/holidays` - Create holiday
- `DELETE /api/v1/routines/holidays/{id}` - Delete holiday

### Export
- `GET /api/v1/routines/export` - Export routine

---

## Key Features

✅ Class Routine Management
✅ Teacher Routine Management
✅ Student Routine Management
✅ Exam Routine Support
✅ Room Management
✅ Laboratory Management
✅ Time Slot Configuration
✅ Period Management
✅ Conflict Detection (Teacher/Room)
✅ Auto Routine Generator
✅ Academic Calendar
✅ Holiday Management
✅ Routine Version Control
✅ Routine Publishing
✅ Export (PDF, Excel, CSV, ICS)
✅ Soft delete
✅ UUID-based public API

---

## Conflict Detection

The system automatically detects:
- Teacher conflicts (same teacher, same time)
- Room conflicts (same room, same time)
- Section conflicts

---

## Routine Format by Day

```json
{
  "0": {
    "day": "Saturday",
    "classes": [
      {
        "time": "08:00 - 08:50",
        "subject": "Mathematics",
        "teacher": "John Doe",
        "room": "Room 101",
        "type": "class"
      }
    ]
  }
}
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| routine.view | View routines |
| routine.create | Create routines |
| routine.update | Update routines |
| routine.delete | Delete routines |
| routine.generate | Generate routines |
| routine.publish | Publish routines |
| routine.calendar | Manage calendar |
| routine.export | Export routines |
| routine.analytics | View analytics |
| routine.approve | Approve routines |

---

## React Structure

```
frontend/src/features/routine/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── routineApi.ts         # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/              # (Ready for components)
```

---

## Next Phase

**Phase 018 - Enterprise Online Admission Management System**

- Admission Campaign
- Online Application
- Payment Gateway
- Merit List
- Student ID Generation

---

## Status

✅ Phase 017 Complete
