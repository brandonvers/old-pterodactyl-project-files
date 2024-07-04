import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/screens/NotFound';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';

import KnowledgebaseContainer from '@/components/dashboard/knowledgebase/KnowledgebaseContainer';
import KnowledgebaseList from '@/components/dashboard/knowledgebase/KnowledgebaseList';
import KnowledgebasePage from '@/components/dashboard/knowledgebase/KnowledgebasePage';

export default ({ location }: RouteComponentProps) => (
    <>
        <NavigationBar/>
        {location.pathname.startsWith('/knowledgebase') &&
        <SubNavigation>
            <div>
                <NavLink to={'/knowledgebase'} exact>Knowledgebase</NavLink>
            </div>
        </SubNavigation>
        }
        <TransitionRouter>
            <Switch location={location}>
                <Route path={'/knowledgebase'} component={KnowledgebaseContainer} exact/>
                <Route path={`/knowledgebase/list/:id`} component={KnowledgebaseList} exact/>
                <Route path={`/knowledgebase/page/:id`} component={KnowledgebasePage} exact/>
                <Route path={'*'} component={NotFound}/>
            </Switch>
        </TransitionRouter>
    </>
);
