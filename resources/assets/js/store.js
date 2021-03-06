export let store = {
    // Shared state object
    state: {
        projects: [],
        contexts: [],
        inbox: {
            tasks: []
        },
        allTasks: {
            tasks: []
        },
        next: {
            tasks: []
        },
        scheduledTaskCounts: {},
        previousRoute: {},
        defaultRoute: {
            name: 'tasks.list',
            params: {'id': 'today'},
            path: '/lists/today'
        }
    }
};