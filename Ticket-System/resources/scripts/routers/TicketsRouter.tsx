import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/screens/NotFound';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import TicketsContainer from '@/components/dashboard/tickets/TicketsContainer';
import ViewTicket from '@/components/dashboard/tickets/ViewTicket';
import CreateTicket from '@/components/dashboard/tickets/CreateTicket';

export default ({ location }: RouteComponentProps) => (
    <>
        <NavigationBar/>
        {location.pathname.startsWith('/tickets') &&
        <SubNavigation>
            <div>
                <NavLink to={'/tickets'} exact>All Tickets</NavLink>
                <NavLink to={'/tickets/new'} exact>New Ticket</NavLink>
            </div>
        </SubNavigation>
        }
        <TransitionRouter>
            <Switch location={location}>
                <Route path={'/tickets'} component={TicketsContainer} exact/>
                <Route path={'/tickets/new'} component={CreateTicket} exact/>
                <Route path={`/tickets/:id`} component={ViewTicket} exact/>
                
                <Route path={'*'} component={NotFound}/>
            </Switch>
        </TransitionRouter>
    </>
);
